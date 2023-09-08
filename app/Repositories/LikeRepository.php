<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Status;
use App\Repositories\Contracts\ILikeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LikeRepository implements ILikeRepository {

    /**
     * Get the all likes
     * @param int $id
     * @param string $type
     * @return Collection
     * @throws \Exception
     */
    public function getLikes(int $id, string $type) :Collection{

        $modelName = 'App\\Models\\' . ucfirst($type);
        $model = $modelName::query()
            ->with('likes')
            ->where('id', $id)
            ->firstOrFail();

        return $model->likes;

    }

    /**
     * Get the like
     * @param int $id
     * @param string $type
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(int $id, string $type) :JsonResponse
    {

        $modelName = 'App\\Models\\' . ucfirst($type);
        $model = $modelName::query()
            ->with('likes')
            ->where('id', $id)
            ->firstOrFail();

        $active = 1;
        $data = Like::query()
            ->where('likeable_id', $id)
            ->where('user_id', Auth::user()->id)
            ->where('type', 1)
            ->where('likeable_type', $model::class)
            ->first();

        if ($data){
            $active = 0;
            $data->delete();
        } else {
            $data = Like::create([
                "user_id"           => Auth::user()->id,
                "type"              => 1,
                "likeable_id"       => $id,
                "likeable_type"     => $model::class,
            ]);
        }

        $model->refresh();
        $count = $model->likes()->count();

        if($data){
            return response()->json([
                'status'    => 1,
                'count'     => $count,
                'active'      => $active,
                'message'   => [__('site.Your comment has been stored successfully')],
            ], Response::HTTP_CREATED);
        }

        throw new \Exception();

    }

    /**
     * Get the count of likes
     * @param int $id
     * @param string $type
     * @return int
     * @throws \Exception
     */
    public function getCount(int $id, string $type) :int {

        $modelName = 'App\\Models\\' . ucfirst($type);
        $model = $modelName::query()
            ->with('likes')
            ->where('id', $id)
            ->firstOrFail();

        return $model->likes()->count();
    }

}
