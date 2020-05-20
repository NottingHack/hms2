<?php

namespace App\Http\Resources\GateKeeper;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GateKeeper\Building as BuildingResource;

class BookableArea extends JsonResource
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
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'maxOccupancy' => $this->getMaxOccupancy(),
            'additionalGuestOccupancy' => $this->getAdditionalGuestOccupancy(),
            'bookingColor' => $this->getBookingColor(),
            'bookingColorString'  => $this->getBookingColorString(),
            'selfBookable' => $this->isSelfBookable(),
            'building' => new BuildingResource($this->getBuilding()),
        ];
    }
}
