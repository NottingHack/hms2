<?php

namespace App\Http\Resources\Gatekeeper;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \HMS\Entities\Gatekeeper\RfidTag
 */
class RfidTagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     *
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
