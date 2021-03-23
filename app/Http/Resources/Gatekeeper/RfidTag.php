<?php

namespace App\Http\Resources\Gatekeeper;

use App\Http\Resources\User as UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RfidTag extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getId(),
            'user' => new UserResource($this->getUser()),
            'rfidSerial' => $this->getRfidSerial(),
            'state' => $this->getState(),
            'stateString' => $this->getStateString(),
            'lastUsed' => $this->getLastUsed(),
            'friendlyName' => $this->getFriendlyName(),
        ];
    }
}
