<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use HMS\Entities\Role;
use HMS\Entities\Banking\Stripe\ChargeType;
use HMS\Entities\Snackspace\TransactionType;
use App\Notifications\Banking\Stripe\ProcessingIssue;
use App\Notifications\Banking\Stripe\DisputeDonationFundsWithdrawn;
use App\Notifications\Banking\Stripe\DisputeSnackspaceFundsWithdrawn;

class HandleChargeDisputeFundsWithdrawnJob extends EventHandler
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
            $softwareTeamRole->notify(new ProcessingIssue($this->webhookCall, 'Dispute Funds Withdrawn'));

            return;
        }

        if ($charge->getType() == ChargeType::SNACKSPACE) {
            // negate the amount
            $amount = -1 * $stripeDispute->amount;

            $stringAmount = money_format('%n', $amount / 100);
            $description = 'Dispute online card payment (funds withdrawn) : ' . $stringAmount;

            $transaction = $this->transactionFactory->create(
                $charge->getUser(),
                $amount,
                TransactionType::ONLINE_PAYMENT,
                $description
            );

            $transaction = $this->transactionRepository->saveAndUpdateBalance($transaction);

            $charge->setWithdrawnTransaction($transaction);
            $charge = $this->chargeRepository->save($charge);

            $disputeSnackspaceFundsWithdrawnNotification = new DisputeSnackspaceFundsWithdrawn($charge, $stripeDispute);

            // Notify User
            // a dispute has been raised over one of your online snackspace payments the funds have been withdrawn
            $charge->getUser()->notify($disputeSnackspaceFundsWithdrawnNotification);

            // notify TEAM_TRUSTEES TEAM_FINANCE
            $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
            $financeTeamRole->notify($disputeSnackspaceFundsWithdrawnNotification);

            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify($disputeSnackspaceFundsWithdrawnNotification);
        } else {
            $disputeDonationFundsWithdrawnNotification = new DisputeDonationFundsWithdrawn($charge, $stripeDispute);

            // notify TEAM_TRUSTEES TEAM_FINANCE
            $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
            $financeTeamRole->notify($disputeDonationFundsWithdrawnNotification);

            $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
            $trusteesTeamRole->notify($disputeDonationFundsWithdrawnNotification);
        }
    }
}
