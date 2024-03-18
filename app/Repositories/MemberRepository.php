<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\Live;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Contracts\IMemberRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class MemberRepository implements IMemberRepository {

    /**
     * Get the new members
     * @return AnonymousResourceCollection
     */
    public function getNewMembers() :AnonymousResourceCollection
    {
        return UserResource::collection(User::query()
            ->whereDoesntHave('followers', function($query) {
                $query->where('follower_id', Auth::user()->id);
            })
            ->where('id', '<>', Auth::user()->id)
            ->with('clubs')
            ->where('status',1)
            ->latest()
            ->limit(10)
            ->get());
    }

    /**
     * Get the congenial members
     * @return AnonymousResourceCollection
     */
    public function getCongenialMembers() :AnonymousResourceCollection
    {

        return UserResource::collection(User::query()
            ->with('clubs')
            ->whereHas('clubs', function ($query) {
                $query->whereIn('clubs.id', array_column(Auth::user()->clubs->toArray(), 'id'));
            })
            ->whereDoesntHave('followers', function($query) {
                $query->where('follower_id', Auth::user()->id);
            })
            ->where('id', '<>', Auth::user()->id)
            ->where('status',1)
            ->limit(10)
            ->get());

    }

    /**
     * Get the member info
     * @param User $user
     * @return array
     */
    public function getMemberInfo(User $user) :array
    {
        $data['postsCount'] = Post::query()
            ->where('user_id', $user->id)
            ->where('status',1)
            ->count();

        $data['viewsCount'] = Post::query()
            ->where('user_id', $user->id)
            ->where('status',1)
            ->sum('view');

        $data['clubs'] = $user->clubs;

        return $data;
    }
}
