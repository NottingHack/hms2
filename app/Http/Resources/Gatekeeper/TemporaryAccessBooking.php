<?php

namespace App\Http\Resources\Gatekeeper;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Gatekeeper\BookableArea as BookableAreaResource;

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
            'userId' => $this->getUser()->getId(),
            'color' => $this->getColor(),
            'bookableArea' => $this->getBookableArea() ? new BookableAreaResource($this->getBookableArea()) : null,
            'approved' => $this->isApproved(),
            $this->mergeWhen(
                \Auth::user() == $this->getUser() || \Gate::allows('gatekeeper.temporaryAccess.view.all'),
                [
                    'title' => $this->getUser()->getFullname(),
                    'notes' => $this->getNotes(),
                    'approvedBy' => $this->getApprovedBy() ? $this->getApprovedBy()->getFullname() : null,
                ]
            ),
        ];
    }
}
