<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use App\Notifications\Banking\Stripe\DonationRefund;
use App\Notifications\Banking\Stripe\ProcessingIssue;
use App\Notifications\Banking\Stripe\SnackspaceRefund;
use HMS\Entities\Banking\Stripe\Charge;
use HMS\Entities\Banking\Stripe\ChargeType;
use HMS\Entities\Role;
use HMS\Entities\Snackspace\TransactionType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Charge as StripeCharge;

class HandleChargeRefundedJob extends EventHandler
{
    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    protected function run()
    {
        $stripeCharge = $this->stripeEvent->data->object;

        if (! $stripeCharge->refunded) {
            // should not be here
            Log::error('HandleChargeRefundedJob: not refunded? :/');

            return true;
        }

        $charge = $this->chargeRepository->findOneById($stripeCharge->id);

        if (is_null($charge)) {
            // TODO: bugger should we create one?
            // for now log it and tell software team
            Log::error('HandleChargeRefundedJob: Charge not found');
            $softwareTeamRole = $this->roleRepository->findOneByName(Role::TEAM_SOFTWARE);
            $softwareTeamRole->notify(new ProcessingIssue($this->webhookCall, 'Charge Refunded'));

            return true;
        }

        $charge->setRefundId($stripeCharge->refunds->data[0]->id);
        $charge = $this->chargeRepository->save($charge);

        switch ($charge->getType()) {
            case ChargeType::SNACKSPACE:
                $ret = $this->snackspacePayment($stripeCharge, $charge);
                break;

            case ChargeType::DONATION:
                $ret = $this->donationPayment($stripeCharge, $charge);
                break;

            default:
                Log::warning('HandleChargeRefundedJob: UnknownChargeType');
                $ret = true;
                break;
        }

        return $ret;
    }

    /**
     * Handle Snackspace Payment.
     *
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param Charge $charge Our Banking instance
     */
    protected function snackspacePayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $user = $charge->getUser();

        // negate the amount
        $amount = -1 * $stripeCharge->amount_refunded;
        $last4 = $stripeCharge->payment_method_details->card->last4;

        $stringAmount = money($amount, 'GBP');
        $description = 'Refund online card payment (' . $last4 . ') : ' . $stringAmount;

        $transaction = $this->transactionFactory->create($user, $amount, TransactionType::ONLINE_PAYMENT, $description);

        $transaction = $this->transactionRepository->saveAndUpdateBalance($transaction);

        $charge->setRefundTransaction($transaction);
        $charge = $this->chargeRepository->save($charge);

        $snackspaceRefundNotification = new SnackspaceRefund($charge, $amount);

        // notify User
        // snackspace payment has been refunded
        $user->notify($snackspaceRefundNotification);

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // a user snackspace payment has been refunded :(
        $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
        $financeTeamRole->notify($snackspaceRefundNotification);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($snackspaceRefundNotification);

        return true;
    }

    /**
     * Handle Donation.
     *
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param Charge $charge Our Banking instance
     */
    protected function donationPayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $user = $charge->getUser();
        // negate the amount
        $amount = -1 * $stripeCharge->amount_refunded;

        $donationRefundNotification = new DonationRefund(
            $charge,
            $stripeCharge,
            $amount
        );

        // notify User
        // your donation has been refunded
        if ($user) {
            $user->notify($donationRefundNotification);
        } else {
            Notification::route('mail', $stripeCharge->receipt_email)
                ->notify($donationRefundNotification);
        }

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // donation has been refunded :(
        $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
        $financeTeamRole->notify($donationRefundNotification);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($donationRefundNotification);

        return true;
    }
}
