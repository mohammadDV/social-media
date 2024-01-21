<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Status;
use App\Models\User;
use App\Repositories\Contracts\ILikeRepository;
use Illuminate\Database\Eloquent\Collection;
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


            if ($model->user_id != Auth::user()->id) {

                switch ($model::class) {
                    case Comment::class;
                        $path = 'comment';
                        $link = '/profile/' . $model->commentable_id;
                        $message = __('site.Someone liked your comment.', ['someone' => Auth::user()->nickname]);
                    break;
                    case Status::class;
                        $path = 'status';
                        $link = '/profile/' . $model->id;
                        $message = __('site.Someone liked your status.', ['someone' => Auth::user()->nickname]);
                    break;
                    case Post::class;
                        $path = 'post';
                        $link = '/news/' . $model->id . '/' . $model->slug;
                        $message = __('site.Someone liked your post.', ['someone' => Auth::user()->nickname]);
                    break;
                    default;
                        $link = '';
                }

                if (!empty($link)) {
                    cache()->remember(
                        'notification.like.' . $path . Auth::user()->id . '.' . $model->id,
                        now()->addMinutes(1),
                        function () use($model, $link, $message) {
                            // Add notification
                            return Notification::create([
                                'message' => $message,
                                'link' => $link,
                                'user_id' => $model->user_id,
                                'model_id' => Auth::user()->id,
                                'model_type' => User::class,
                            ]);
                        });
                }
            }


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
