<?php

namespace App\Repositories;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\Live;
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

        $data['followersCount'] = Follow::select('follower_id')->where('user_id', $userId)->count();
        $followers = array_column(Follow::query()
            ->select('follower_id')
            ->where('user_id', $userId)
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray(), 'follower_id');

        $data['followers'] = UserResource::collection(User::whereIn('id', $followers)->where('status',1)->get());

        $data['followingsCount'] = Follow::select('user_id')->where('follower_id', $userId)->count();
        $followings = array_column(Follow::query()
            ->select('user_id')
            ->where('follower_id', $userId)
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray(), 'user_id');

        $data['followings'] = UserResource::collection(User::whereIn('id', $followings)->where('status',1)->get());

        return $data;

    }

    /**
     * Get the followers
     * @param int $userId
     * @param SearchRequest $request
     * @return LengthAwarePaginator
     */
    public function getFollowers(int $userId, SearchRequest $request) :LengthAwarePaginator
    {
        return Follow::query()
            ->with('user')
            ->where('user_id', $userId)
            ->paginate(10);
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
            ->whereHas('followers', function ($query) use ($userId) {
                $query->where('follower_id', $userId);
            })
            ->when(!empty($request->search) , function ($query) use ($request) {
                $query->where('first_name', "like", "%" . $request->search . "%");
                $query->orWhere('last_name', "like", "%" . $request->search . "%");
                $query->orWhere('nickname', "like", "%" . $request->search . "%");
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
        }

        return [
            'follow' => $active,
            'status' => 1,
            'active' => $active,
            'message' => __('site.The operation has been successfully')
        ];
    }
}
