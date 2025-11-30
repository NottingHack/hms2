<?php

namespace App\Http\Resources\Gatekeeper;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @mixin \HMS\Entities\Gatekeeper\TemporaryAccessBooking
 */
class TemporaryAccessBookingResource extends JsonResource
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
        $allowSensative = false; // default anonymization
        $allowAll = false; // default anonymization

        if (Gate::allows('gatekeeper.temporaryAccess.view.all')) {
            $allowSensative = true;
            $allowAll = true;
        } elseif (Auth::user() == $this->getUser() && Gate::allows('gatekeeper.temporaryAccess.view.self')) {
            $allowSensative = true;
        }

        return [
            'id' => $this->getId(),
            'start' => $this->getStart(),
            'end' => $this->getEnd(),
            'userId' => $this->getUser()->getId(),
            'color' => $this->getColor(),
            'bookableArea' => $this->getBookableArea() ? new BookableAreaResource($this->getBookableArea()) : null,
            'approved' => $this->isApproved(),
            'guests' => $this->getGuests(),
            $this->mergeWhen(
                $allowSensative,
                [
                    'userName' => $this->getUser()->getFullname(),
                    'notes' => $this->getNotes(),
                    'approvedById' => $this->getApprovedBy() ? $this->getApprovedBy()->getId() : null,
                ]
            ),
            $this->mergeWhen(
                $allowAll,
                [
                    'approvedByName' => $this->getApprovedBy() ? $this->getApprovedBy()->getFullname() : null,
                ]
            ),
        ];
    }
}
