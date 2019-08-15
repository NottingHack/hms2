<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use HMS\Entities\Role;
use App\Notifications\Banking\Stripe\DisputeCreated;
use App\Notifications\Banking\Stripe\ProcessingIssue;

class HandleChargeDisputeCreatedJob extends EventHandler
{
    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    protected function run()
    {
        $stripeDispute = $this->stripeEvent->data->object;
        $chargeId = $stripeDispute->charge;

        // find charge
        $charge = $this->chargeRepository->findOneById($chargeId);

        if (is_null($charge)) {
            // TODO: bugger should we create one?
            // for now log it and tell software team
            \Log::error('HandleChargeRefundedJob: Charge not found');
            $softwareTeamRole = $this->roleRepository->findOneByName(Role::SOFTWARE_TEAM);
            $softwareTeamRole->notify(new ProcessingIssue($this->webhookCall, 'Dispute Created'));

            return true;
        }

        $charge->setDisputeId($stripeDispute->id);
        $charge = $this->chargeRepository->save($charge);

        $disputeCreatedNotification = new DisputeCreated($charge, $stripeDispute);

        // notify TEAM_TRUSTEES TEAM_FINANCE
        $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
        $financeTeamRole->notify($disputeCreatedNotification);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($disputeCreatedNotification);

        return true;
    }
}
