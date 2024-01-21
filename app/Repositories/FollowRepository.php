<?php

namespace App\Repositories;

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
     * @param int $userId
     * @return array
     */
    public function index(int $userId) :array
    {

        $data['followersCount'] = Follow::select('follower_id')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->count();

        $data['followers'] = UserResource::collection(
            User::query()
            ->where('status',1)
            ->whereHas('following', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->limit(12)
            ->get()
        );

        $data['followingsCount'] = Follow::select('user_id')->where('follower_id', $userId)->count();

        $data['followings'] = UserResource::collection(
            User::query()
            ->where('status',1)
            ->whereHas('followers', function ($query) use ($userId) {
                $query->where('follower_id', $userId);
            })
            ->limit(12)
            ->get()
        );

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
     * @param int $userId
     * @param SearchRequest $request
     * @return LengthAwarePaginator
     */
    public function getFollowers(int $userId, SearchRequest $request) :LengthAwarePaginator
    {

        return User::query()
            ->where('status',1)
            ->whereHas('following', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where(function ($query) use ($request) {
                $query->where('first_name', "like", "%" . $request->search . "%")
                    ->orWhere('last_name', "like", "%" . $request->search . "%")
                    ->orWhere('nickname', "like", "%" . $request->search . "%");
            })
            ->with('followers')
            ->paginate(12);
    }

    /**
     * Get the followers
     * @param int $userId
     * @param SearchRequest $request
     * @return LengthAwarePaginator
     */
    public function getFollowings(int $userId, SearchRequest $request) :LengthAwarePaginator
    {
        return User::query()
            ->where('status',1)
            ->whereHas('followers', function ($query) use ($userId) {
                $query->where('follower_id', $userId);
            })
            ->where(function ($query) use ($request) {
                $query->where('first_name', "like", "%" . $request->search . "%")
                    ->orWhere('last_name', "like", "%" . $request->search . "%")
                    ->orWhere('nickname', "like", "%" . $request->search . "%");
            })
            ->with('following')
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
        $data   = Follow::query()
            ->where('follower_id', Auth::user()->id)
            ->where('user_id',$user->id)
            ->first();

        if ($data){
            $active = 0;
            $data->delete();
        }else{
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
