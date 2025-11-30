<?php

namespace App\Http\Resources\Gatekeeper;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \HMS\Entities\Gatekeeper\BookableArea
 */
class BookableAreaResource extends JsonResource
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
