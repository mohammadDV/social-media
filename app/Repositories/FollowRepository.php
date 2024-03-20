<?php

namespace App\Repositories;

use App\Http\Requests\FollowChangeStatusRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\Live;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\IFollowRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FollowRepository implements IFollowRepository {

    /**
     * Get the followers and followings
     * @param User $user
     * @return array
     */
    public function index(User $user) :array
    {

        $user->is_private = Follow::query()
            ->where('follower_id', Auth::user()->id)
            ->where('user_id', $user->id)
            ->where('status', Follow::STATUS_ACCEPTED)
            ->count() == 0 && $user->is_private == 1 && Auth::user()->id != $user->id ? true : false;

        $data['info'] = $user;

        $data['followersCount'] = Follow::select('follower_id')
            ->where('user_id', $user->id)
            ->where('status', Follow::STATUS_ACCEPTED)
            ->orderBy('id', 'desc')
            ->count();

        $data['followers'] = Follow::query()
            ->where('status', Follow::STATUS_ACCEPTED)
            ->where('user_id', $user->id)
            ->with('follower')
            ->whereHas('follower', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('status', 1);
                });
            })
            ->limit(12)
            ->get();

        $data['followingsCount'] = Follow::select('user_id')
            ->where('status', Follow::STATUS_ACCEPTED)
            ->where('follower_id', $user->id)
            ->where('status', Follow::STATUS_ACCEPTED)
            ->count();

        $data['followings'] = Follow::query()
            ->where('status', Follow::STATUS_ACCEPTED)
            ->where('follower_id', $user->id)
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('status', 1);
                });
            })
            ->limit(12)
            ->get();

        return $data;

    }

    /**
     * Specify whether to be a follower or not.
     * @param User $user
     * @return JsonResponse
     */
    public function isFollower(User $user): array
    {
        if (Auth::user()->id == $user->id) {
            return [
                'active' => 1,
            ];
        }

        $follow = Follow::query()
            ->where('user_id', $user->id)
            ->where('follower_id', Auth::user()->id)
            ->first();

        return [
            'active' => !empty($follow->user_id)
        ];
    }

    /**
     * Get the followers
     * @param User $user
     * @param SearchRequest $request
     */
    public function getFollowers(User $user, SearchRequest $request)
    {

        if (Follow::query()
            ->where('follower_id', Auth::user()->id)
            ->where('user_id', $user->id)
            ->where('status', Follow::STATUS_ACCEPTED)
            ->count() == 0 && $user->is_private == 1 && Auth::user()->id != $user->id) {
                return [];
            }

        return Follow::query()
            ->where('user_id', $user->id)
            ->with('follower')
            ->whereHas('follower', function ($query) use($request) {
                $query->where(function ($subQuery) {
                    $subQuery->where('status', 1);
                });
                $query->where(function ($subQuery) use($request) {
                    $subQuery->where('first_name', "like", "%" . $request->search . "%")
                        ->orWhere('last_name', "like", "%" . $request->search . "%")
                        ->orWhere('nickname', "like", "%" . $request->search . "%");
                });
            })
            ->paginate(12);
    }

    /**
     * Get the followers
     * @param User $user
     * @param SearchRequest $request
     */
    public function getFollowings(User $user, SearchRequest $request)
    {
        if (Follow::query()
            ->where('follower_id', Auth::user()->id)
            ->where('user_id', $user->id)
            ->where('status', Follow::STATUS_ACCEPTED)
            ->count() == 0 && $user->is_private == 1 && Auth::user()->id != $user->id) {
                return [];
            }

        return Follow::query()
            ->where('status', Follow::STATUS_ACCEPTED)
            ->where('follower_id', $user->id)
            ->with('user')
            ->whereHas('user', function ($query) use($request) {
                $query->where(function ($subQuery) {
                    $subQuery->where('status', 1);
                });
                $query->where(function ($subQuery) use($request) {
                    $subQuery->where('first_name', "like", "%" . $request->search . "%")
                        ->orWhere('last_name', "like", "%" . $request->search . "%")
                        ->orWhere('nickname', "like", "%" . $request->search . "%");
                });
            })
            ->paginate(12);
    }

    /**
     * Store the follow
     * @param User $user
     * @return array
     */
    public function store(User $user) :array
    {
        if (Auth::user()->id == $user->id) {
            return [
                'follow' => 1,
                'status' => 1,
                'active' => 1,
                'message' => __('site.The operation has been successfully')
            ];
        }

        $active = 1;
        $data = Follow::query()
            ->where('follower_id', Auth::user()->id)
            ->where('user_id', $user->id)
            ->first();

        if ($data) {
            $active = 0;
            $data->delete();
        } else {
            $data = Follow::create([
                "follower_id" => Auth::user()->id,
                "user_id" => $user->id,
            ]);

            cache()->remember(
                'notification.follow.user' . Auth::user()->id . '.' . $user->id,
                now()->addMinutes(1),
                function () use($user) {
                    // Add notification
                    return Notification::create([
                        'message' => __('site.Someone sent a request to you.', ['someone' => Auth::user()->nickname]),
                        'link' => '/profile/followers',
                        'user_id' => $user->id,
                        'model_id' => Auth::user()->id,
                        'model_type' => User::class,
                        'has_email' => 1,
                    ]);
                });

        }

        return [
            'follow' => $active,
            'status' => 1,
            'active' => $active,
            'message' => __('site.The operation has been successfully')
        ];
    }

    /**
     * Chaneg status of the follow
     * @param User $user
     * @param FollowChangeStatusRequest $request
     * @return array
     * @throws \Exception
     */
    public function changeFollowStatus(User $user, FollowChangeStatusRequest $request) :array
    {
        if (Auth::user()->id == $user->id) {
            return [
                'follow' => 1,
                'status' => 1,
                'active' => 1,
                'message' => __('site.The operation has been successfully')
            ];
        }

        $active = 1;

        $data = Follow::query()
            ->where('user_id', Auth::user()->id)
            ->where('follower_id', $user->id)
            ->first();

        if (is_null($data)){
            throw new \Exception('Not found the follow');
        }

        if ($request->status == Follow::STATUS_REJECTED) {
            $active = 0;
            $data->delete();
        } else {
            $data->update([
                'status' => Follow::STATUS_ACCEPTED
            ]);

            cache()->remember(
                'notification.follow.accepted' . Auth::user()->id . '.' . $user->id,
                now()->addMinutes(1),
                function () use($user) {
                    // Add notification
                    return Notification::create([
                        'message' => __('site.Someone accepted your request.', ['someone' => Auth::user()->nickname]),
                        'link' => '/member/' . Auth::user()->id,
                        'user_id' => $user->id,
                        'model_id' => Auth::user()->id,
                        'model_type' => User::class,
                        'has_email' => 1,
                    ]);
                });

        }

        return [
            'follow' => $active,
            'status' => 1,
            'active' => $active,
            'message' => __('site.The operation has been successfully')
        ];
    }
}
