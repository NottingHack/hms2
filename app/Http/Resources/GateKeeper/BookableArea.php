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
        return parent::toArray($request);
    }
}
