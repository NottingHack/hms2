<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpenIdUserResource extends JsonResource
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
        $resource = [
            'sub' => str($this->getId()),
            'name' => $this->getFullname(),
            'given_name' => $this->getFirstname(),
            'family_name' => $this->getLastname(),
            'preferred_username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'email_verified' => $this->hasVerifiedEmail(),
            'updated_at' => $this->getUpdatedAt()->getTimestamp(),
            // 'profile' => new ProfileResource($this->getProfile()),
            // 'memberStatusString' => $this->getMemberStatusString(),
            'grafanaRole' => 'Viewer', // 'No basic role',
        ];

        if ($this->can('grafana.grafanaAdmin')) {
            $resource['grafanaRole'] = 'GrafanaAdmin';
        } elseif ($this->can('grafana.admin')) {
            $resource['grafanaRole'] = 'Admin';
        } elseif ($this->can('grafana.editor')) {
            $resource['grafanaRole'] = 'Editor';
        } elseif ($this->can('grafana.viewer')) {
            $resource['grafanaRole'] = 'Viewer';
        }

        return $resource;
    }
}
