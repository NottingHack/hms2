<?php

namespace App\Http\Resources\Members;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \HMS\Entities\Members\Project
 */
class ProjectResource extends JsonResource
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
            'projectName' => $this->getProjectName(),
            'description' => $this->getDescription(),
            'startDate' => $this->getStartDate(),
            'completeDate' => $this->getCompleteDate(),
            'state' => $this->getState(),
            'stateString' => $this->getStateString(),
            'userId' => $this->getUser()->getId(),
        ];
    }
}
