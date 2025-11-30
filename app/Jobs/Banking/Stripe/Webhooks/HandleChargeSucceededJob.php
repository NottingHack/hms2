<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use App\Notifications\Banking\Stripe\DonationPayment;
use App\Notifications\Banking\Stripe\SnackspacePayment;
use HMS\Entities\Banking\Stripe\Charge;
use HMS\Entities\Banking\Stripe\ChargeType;
use HMS\Entities\Role;
use HMS\Entities\Snackspace\TransactionType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Stripe\Charge as StripeCharge;

class HandleChargeSucceededJob extends EventHandler
{
    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    protected function run()
    {
        $stripeCharge = $this->stripeEvent->data->object;
        $type = strtoupper($stripeCharge->metadata->type);

        $charge = $this->chargeFactory->create($stripeCharge->id, $stripeCharge->amount, $type);
        $this->chargeRepository->save($charge);

        switch ($type) {
            case ChargeType::SNACKSPACE:
                $ret = $this->snackspacePayment($stripeCharge, $charge);
                break;

            case ChargeType::DONATION:
                $ret = $this->donationPayment($stripeCharge, $charge);
                break;

            default:
                Log::warning('HandleChargeSucceededJob: UnknownChargeType');
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
     *
     * @return bool
     */
    protected function snackspacePayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $userId = $stripeCharge->metadata->user_id;
        $user = $this->userRepository->findOneById($userId);

        $amount = $stripeCharge->amount;
        $last4 = $stripeCharge->payment_method_details->card->last4;

        $stringAmount = money($amount, 'GBP');
        $description = 'Online card payment (' . $last4 . ') : ' . $stringAmount;

        $transaction = $this->transactionFactory->create($user, $amount, TransactionType::ONLINE_PAYMENT, $description);

        $transaction = $this->transactionRepository->saveAndUpdateBalance($transaction);

        $charge->setUser($user);
        $charge->setTransaction($transaction);
        $charge = $this->chargeRepository->save($charge);

        // notify User
        $user->notify(new SnackspacePayment($charge));

        return true;
    }

    /**
     * Handle Donation.
     *
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param Charge $charge Our Banking instance
     *
     * @return bool
     */
    protected function donationPayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $userId = optional($stripeCharge->metadata)->user_id;
        $user = null;
        if ($userId) {
            $user = $this->userRepository->findOneById($userId);
        }

        if ($user) {
            $charge->setUser($user);
            $charge = $this->chargeRepository->save($charge);
        }

        $donationPaymentNotification = new DonationPayment(
            $charge,
            $stripeCharge
        );

        // notify User
        if ($user) {
            $user->notify($donationPaymentNotification);
        } else {
            Notification::route('mail', $stripeCharge->receipt_email)
                ->notify($donationPaymentNotification);
        }

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // someone has just made a donation to the space :)
        $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
        $financeTeamRole->notify($donationPaymentNotification);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($donationPaymentNotification);

        return true;
    }
}
