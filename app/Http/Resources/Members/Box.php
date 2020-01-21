<?php

namespace App\Http\Resources\Members;

use Illuminate\Http\Resources\Json\JsonResource;

class Box extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getId(),
            'boughtDate' => $this->getBoughtDate(),
            'removedDate' => $this->getRemovedDate(),
            'state' => $this->getState(),
            'stateString' => $this->getStateString(),
            'userId' => $this->getUser()->getId(),
        ];
    }
}
