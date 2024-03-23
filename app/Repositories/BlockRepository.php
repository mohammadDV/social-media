<?php

namespace App\Repositories;

use App\Http\Requests\SearchRequest;
use App\Models\Block;
use App\Models\Follow;
use App\Models\User;
use App\Repositories\Contracts\IBlockRepository;
use Illuminate\Support\Facades\Auth;

class BlockRepository implements IBlockRepository {

    /**
     * Get the blockers and blockings
     * @param SearchRequest $request
     */
    public function index(SearchRequest $request)
    {

        return User::query()
            ->whereHas('blocks', function ($query) {
                $query->where('blocker_id', Auth::user()->id);
            })
            ->where(function ($query) use($request) {
                $query->where('first_name', "like", "%" . $request->search . "%")
                ->orWhere('last_name', "like", "%" . $request->search . "%")
                ->orWhere('nickname', "like", "%" . $request->search . "%");
            })
            ->paginate(12);

    }

    /**
     * Store the block
     * @param User $user
     * @return array
     */
    public function store(User $user) :array
    {
        if (Auth::user()->id == $user->id) {
            return [
                'status' => 1,
                'block' => 0,
                'message' => __('site.The operation has been successfully')
            ];
        }

        $block = 1;
        $data = Block::query()
            
            ->where('blocker_id', Auth::user()->id)
            ->where('user_id', $user->id)
            ->first();

        if ($data) {
            $block = 0;
            $data->delete();
        } else {
            $data = Block::create([
                "blocker_id" => Auth::user()->id,
                "user_id" => $user->id,
            ]);

            $data = Follow::query()
                ->where([
                    ['follower_id', Auth::user()->id],
                    ['user_id', $user->id],
                ])
                ->orWhere([
                    ['follower_id', $user->id],
                    ['user_id', Auth::user()->id],
                ])
                ->delete();
        }

        return [
            'status' => 1,
            'block' => $block,
            'message' => __('site.The operation has been successfully')
        ];
    }
}
