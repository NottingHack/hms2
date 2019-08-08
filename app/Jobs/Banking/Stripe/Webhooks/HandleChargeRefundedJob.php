<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use Stripe\Event;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use Stripe\Charge as StripeCharge;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use HMS\Entities\Banking\Stripe\Charge;
use Illuminate\Queue\InteractsWithQueue;
use HMS\Entities\Banking\Stripe\ChargeType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Entities\Snackspace\TransactionType;
use Spatie\WebhookClient\Models\WebhookCall;
use HMS\Factories\Snackspace\TransactionFactory;
use App\Notifications\Banking\Stripe\DonationRefund;
use HMS\Repositories\Banking\Stripe\ChargeRepository;
use App\Notifications\Banking\Stripe\SnackspaceRefund;
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
        $this->chargeRepository = $chargeRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;

        $event = Event::constructFrom($this->webhookCall->payload);
        $stripeCharge = $event->data->object;

        if (! $stripeCharge->refunded) {
            // should not be here
            \Log::error('HandleChargeRefundedJob: not refunded? :/');

            return;
        }

        $charge = $chargeRepository->findOneById($stripeCharge->id);

        if (is_null($charge)) {
            // TODO: bugger should we create one?
            \Log::error('HandleChargeRefundedJob: Charge not found');

            return;
        }

        $charge->setRefundId($stripeCharge->refunds->data[0]->id);
        dump($charge);
        $charge = $this->chargeRepository->save($charge);
        dump($charge);

        switch ($charge->getType()) {
            case ChargeType::SNACKSPACE:
                $this->snackspacePayment($stripeCharge, $charge);
                break;

            case ChargeType::DONATION:
                $this->donationPayment($stripeCharge, $charge);
                break;

            default:
                \Log::warning('HandleChargeRefundedJob: UnknownChargeType');
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
        dump($charge);
        $user = $charge->getUser();

        $amount = -$stripeCharge->amount_refunded;
        $last4 = $stripeCharge->payment_method_details->card->last4;

        $stringAmount = money_format('%n', $amount / 100);
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
        $amount = -$stripeCharge->amount_refunded;

        $donationRefundNotification = new DonationRefund($charge, $amount);

        // notify User
        // your donation has been refunded
        $user->notify($donationRefundNotification);

        // notify TEAM_TRUSTEES TEAM_FINANCE
        // donation has been refunded :(
        $financeTeamRole = $this->roleRepository->findOneByName(Role::TEAM_FINANCE);
        $financeTeamRole->notify($donationRefundNotification);

        $trusteesTeamRole = $this->roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($donationRefundNotification);
    }
}
