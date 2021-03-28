<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'user_id' => $this->getUser()->getId(),
            'joinDate' => $this->getJoinDate() ? $this->getJoinDate()->toDateString() : null,
            'creditLimit' => $this->getCreditLimit(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'address3' => $this->getAddress3(),
            'addressCity' => $this->getAddressCity(),
            'addressCounty' => $this->getAddressCounty(),
            'addressPostcode' => $this->getAddressPostcode(),
            'contactNumber' => $this->getContactNumber(),
            'balance' => $this->getBalance(),
            'votingPreference' => $this->getVotingPreference(),
            'votingPreferenceStatedAt' => $this->getVotingPreferenceStatedAt(),
        ];
    }
}
