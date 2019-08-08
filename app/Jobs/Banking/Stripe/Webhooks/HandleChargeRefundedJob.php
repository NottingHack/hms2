<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use Stripe\Event;
use Illuminate\Bus\Queueable;
use Stripe\Charge as StripeCharge;
use HMS\Repositories\UserRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use HMS\Entities\Banking\Stripe\ChargeType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\WebhookClient\Models\WebhookCall;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Snackspace\TransactionRepository;

class HandleChargeRefundedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebhookCall
     */
    protected $webhookCall;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new job instance.
     *
     * @param WebhookCall $webhookCall
     *
     * @return void
     */
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    /**
     * Execute the job.
     *
     * @param ChargeRepository $chargeRepository
     * @param TransactionRepository $transactionRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        ChargeRepository $chargeRepository,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;

        $event = Event::constructFrom($this->webhookCall->payload);
        $stripeCharge = $event->data->object;

        if (! $stripeCharge->refunded) {
            // should not be here
            return;
        }

        $charge = $chargeRepository->findOneById($stripeCharge->id);

        if (is_null($charge)) {
            // TODO: bugger should we create one?
            return;
        }

        switch ($charge->getType()) {
            case ChargeType::SNACKSPACE:
                $this->snackspacePayment($stripeCharge, $charge);
                break;

            case ChargeType::DONATION:
                $this->donationPayment($stripeCharge, $charge);
                break;

            default:
                break;
        }
    }

    /**
     * Handel Snackspace Payment.
     *
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param Charge $chargen Our Banking instance
     */
    public function snackspacePayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $user = $charge->getUser();

        $amount = -$stripeCharge->amount_refunded;
        $last4 = $stripeCharge->payment_method_details->card->last4;

        $stringAmount = money_format('%n', $amount / 100);
        $description = 'Refund online card payment (' . $last4 . ') : ' . $stringAmount;

        $transaction = $this->transactionFactory->create($user, $amount, TransactionType::ONLINE_PAYMENT, $description);

        $transaction = $this->transactionRepository->saveAndUpdateBalance($transaction);

        // notify User
        // snackspace payment has been refunded

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // a user snackspace payment has been refunded :(
    }

    /**
     * Handel Donation.
     *
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param Charge $chargen Our Banking instance
     */
    public function donationPayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $user = $charge->getUser();

        // notify User
        // your donation has been refunded

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // donation has been refunded :(
    }
}
