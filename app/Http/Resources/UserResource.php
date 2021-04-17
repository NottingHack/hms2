<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
            'fullname' => $this->getFullname(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'profile' => new ProfileResource($this->getProfile()),
            'memberStatusString' => $this->getMemberStatusString(),
        ];
    }
}
