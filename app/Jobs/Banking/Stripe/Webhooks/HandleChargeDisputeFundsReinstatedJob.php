<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use HMS\Entities\Role;
use HMS\Entities\Banking\Stripe\ChargeType;
use HMS\Entities\Snackspace\TransactionType;
use App\Notifications\Banking\Stripe\ProcessingIssue;
use App\Notifications\Banking\Stripe\DisputeDonationFundsReinstated;
use App\Notifications\Banking\Stripe\DisputeSnackspaceFundsReinstated;

class HandleChargeDisputeFundsReinstatedJob extends EventHandler
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
            $softwareTeamRole->notify(new ProcessingIssue($this->webhookCall, 'Dispute Funds Reinstated'));

            return true;
        }

        if ($charge->getType() == ChargeType::SNACKSPACE) {
            $amount = $stripeDispute->amount;

            $stringAmount = money_format('%n', $amount / 100);
            $description = 'Dispute online card payment (funds reinstated) : ' . $stringAmount;

            $transaction = $this->transactionFactory->create(
                $charge->getUser(),
                $amount,
                TransactionType::ONLINE_PAYMENT,
                $description
            );

            $this->transactionRepository->saveAndUpdateBalance($transaction);

            $charge->setReinstatedTransaction($transaction);
            $charge = $this->chargeRepository->save($charge);

            $disputeSnackspaceFundsReinstatedNotification =
                new DisputeSnackspaceFundsReinstated($charge, $stripeDispute);

            // Notify User
            // a dispute has been raised over one of your online snackspace payments the funds have been reinstanted
            $charge->getUser()->notify($disputeSnackspaceFundsReinstatedNotification);

            // notify TEAM_TRUSTEES TEAM_FINANCE
            $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
            $financeTeamRole->notify($disputeSnackspaceFundsReinstatedNotification);

            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify($disputeSnackspaceFundsReinstatedNotification);
        } else {
            $disputeDonationFundsReinstatedNotification = new DisputeDonationFundsReinstated($charge, $stripeDispute);

            // notify TEAM_TRUSTEES TEAM_FINANCE
            $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
            $financeTeamRole->notify($disputeDonationFundsReinstatedNotification);

            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify($disputeDonationFundsReinstatedNotification);
        }

        return true;
    }
}
