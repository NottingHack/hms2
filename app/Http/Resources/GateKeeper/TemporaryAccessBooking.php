<?php

namespace App\Http\Resources\GateKeeper;

use Illuminate\Http\Resources\Json\JsonResource;

class TemporaryAccessBooking extends JsonResource
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
            'start' => $this->getStart(),
            'end' => $this->getEnd(),
            'title' => $this->getUser()->getFullName(),
            'userId' => $this->getUser()->getId(),
            'color' => $this->getColor(),
            'notes' => $this->getNotes(),
        ];
    }
}
