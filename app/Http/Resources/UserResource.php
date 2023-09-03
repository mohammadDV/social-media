<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nickname' => $this->nickname,
            'mobile' => $this->mobile,
            'biography' => $this->biography,
            'profile_photo_path' => $this->profile_photo_path,
            'bg_photo_path' => $this->bg_photo_path,
            'national_code' => $this->national_code,
            'point' => $this->point,
            'status' => $this->status,
            'status' => $this->status,
            'clubs' => new ClubResource($this->whenLoaded('clubs')),
        ];
    }
}
