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
            'email' => $this->email,
            'fullname' => !empty($this->nickname) ? $this->nickname : trim($this->first_name . ' ' . $this->last_name),
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
            'is_private' => $this->is_private == 1,
            'is_report' => $this->is_report,
            'created_at' => $this->created_at,
            'is_admin' => $this->level == 3,
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getPermissionRoleNames(),
            'clubs' => new ClubResource($this->whenLoaded('clubs')),
        ];
    }
}
