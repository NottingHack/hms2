<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use Stripe\Event;
use Illuminate\Bus\Queueable;
use Stripe\Charge as StripeCharge;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Queue\SerializesModels;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Entities\Snackspace\TransactionType;
use Spatie\WebhookClient\Models\WebhookCall;
use HMS\Factories\Banking\Stripe\ChargeFactory;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Banking\Stripe\ChargeRepository;
use HMS\Repositories\Snackspace\TransactionRepository;

class HandleChargeSucceededJob implements ShouldQueue
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
     * @var ChargeRepository
     */
    protected $chargeRepository;

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
     * @param UserRepository $userRepository
     * @param ChargeFactory $chargeFactory
     * @param ChargeRepository $chargeRepository
     * @param TransactionFactory $transactionFactory
     * @param TransactionRepository $transactionRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        UserRepository $userRepository,
        ChargeFactory $chargeFactory,
        ChargeRepository $chargeRepository,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->chargeRepository = $chargeRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;

        $event = Event::constructFrom($this->webhookCall->payload);
        $stripeCharge = $event->data->object;
        $type = $stripeCharge->metadata->type;

        $charge = $chargeFactory->create($stripeCharge->id, $stripeCharge->amount, $type);
        $this->chargeRepository->save($charge);

        switch ($type) {
            case 'snackspace':
                $this->snackspacePayment($stripeCharge, $charge);
                break;

            case 'donation':
                $this->snackspacePayment($stripeCharge, $charge);
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
        $userId = $stripeCharge->metadata->user_id;
        $user = $this->userRepository->findOneById($userId);

        $amount = $stripeCharge->amount;
        $last4 = $stripeCharge->payment_method_details->card->last4;

        $stringAmount = money_format('%n', $amount / 100);
        $description = 'Online card payment (' . $last4 . ') : ' . $stringAmount;

        $transaction = $this->transactionFactory->create($user, $amount, TransactionType::ONLINE_PAYMENT, $description);

        $transaction = $this->transactionRepository->saveAndUpdateBalance($transaction);

        $charge->setUser($user);
        $charge->setTransaction($transaction);
        $this->chargeRepository->save($charge);

        // notify User
    }

    /**
     * Handel Donation.
     *
     * @param StripeCharge $stripeCharge Stripe/Charge instance
     * @param Charge $chargen Our Banking instance
     */
    public function donationPayment(StripeCharge $stripeCharge, Charge $charge)
    {
        $userId = $stripeCharge->metadata->user_id;
        $user = $this->userRepository->findOneById($userId);

        $charge->setUser($user);
        $this->chargeRepository->save($charge);

        // notify User
        // thanks for your donation

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // someone has just made a donation to the space :)
    }
}
