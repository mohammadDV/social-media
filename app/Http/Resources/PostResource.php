<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'content' => $this->content,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'slide' => $this->slide,
            'image' => $this->image,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'view' => $this->view,
            'type' => $this->type,
            'special' => $this->special,
            'video' => $this->video,
            'advertise' => $this->whenLoaded('advertise'),
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'nickname' => $this->user->nickname,
                    'profile_photo_path' => $this->user->profile_photo_path,
                ];
            }),
            'created_at' => $this->created_at,
            'comments' => CommentResource::collection($this->comments),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'category' => $this->category,
        ];
    }
}
