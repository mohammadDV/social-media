<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pre_title' => $this->pre_title,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'content' => $this->when(
                array_key_exists('content', $this->resource->getAttributes()),
                fn () => $this->content
            ),
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'slide' => $this->slide,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'view' => $this->view,
            'type' => $this->type,
            'special' => $this->special,
            'video' => $this->video,
            'advertise' => $this->whenLoaded('advertise'),
            'user' => $this->whenLoaded('user', function () {
                $user = $this->user;
                if (! $user instanceof User) {
                    return null;
                }

                return [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                    'profile_photo_path' => $user->profile_photo_path,
                ];
            }),
            'created_at' => $this->created_at,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'category' => $this->resource->getAttribute('category'),
        ];
    }
}
