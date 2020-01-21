<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Profile as ProfileResource;

class User extends JsonResource
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
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'profile' => new ProfileResource($this->getProfile()),
        ];
    }
}
