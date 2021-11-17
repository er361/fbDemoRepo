<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role' => $this->role,
            'display_name' => $this->display_name,
            'tags' => $this->tags->pluck('name'),
            'teamleads' => $this->teamleads,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
