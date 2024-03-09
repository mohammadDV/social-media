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
            'category_id' => $this->category_id, // Assuming you have a category_id field
            'view' => $this->view,
            'type' => $this->type,
            'special' => $this->special,
            'video' => $this->video,
            'advertise' => $this->whenLoaded('advertise'),
            'created_at' => $this->created_at,
            'comments' => CommentResource::collection($this->comments),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'category' => $this->category,
        ];
    }
}
