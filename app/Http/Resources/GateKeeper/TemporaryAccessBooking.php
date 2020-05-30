<?php

namespace App\Http\Resources\GateKeeper;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GateKeeper\BookableArea as BookableAreaResource;

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
            'bookableArea' => $this->getBookableArea() ? new BookableAreaResource($this->getBookableArea()) : null,
            'approved' => $this->isApproved(),
            'approvedBy' => $this->getApprovedBy() ? $this->getApprovedBy()->getFullName() : null,
        ];
    }
}
