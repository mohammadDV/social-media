<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'title' => $this->title,
            'text' => $this->title,
            'slug' => $this->slug,
            'image' => $this->image,
            'posts_count' => $this->when(!is_null($this->posts_count), $this->posts_count),
        ];;
    }
}
