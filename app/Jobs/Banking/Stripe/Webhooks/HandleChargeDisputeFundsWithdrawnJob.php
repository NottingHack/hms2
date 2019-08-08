<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use Stripe\Event;
use Illuminate\Bus\Queueable;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use HMS\Entities\Banking\Stripe\ChargeType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\WebhookClient\Models\WebhookCall;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Snackspace\TransactionRepository;

class HandleChargeDisputeFundsWithdrawnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebhookCall
     */
    protected $webhookCall;

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
     * @param TransactionFactory $transactionFactory
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
        $event = Event::constructFrom($this->webhookCall->payload);
        $stripeDispute = $event->data->object;
        $chargeId = $stripeDispute->charge;

        // find charge
        $charge = $chargeRepository->findOneById($chargeId);

        if (is_null($charge)) {
            // TODO: bugger should we create one?
            return;
        }

        if ($charge->getType() == ChargeType::SNACKSPACE) {
            $amount = -$stripeDispute->amount;

            $stringAmount = money_format('%n', $amount / 100);
            $description = 'Dispute online card payment (funds withdrawn) : ' . $stringAmount;

            $transaction = $transactionFactory->create(
                $charge->getUser(),
                $amount,
                TransactionType::ONLINE_PAYMENT,
                $description
            );

            $transaction = $transactionRepository->saveAndUpdateBalance($transaction);

            $charge->setWithdrawnTransaction($transaction);
            $chargeRepository->save($charge);

        // Notify User
            // a dispute has been raised over one of your online snackspace payments the funds have been withdrawn

            // notify TEAM_TRUSTEES TEAM_FINANCE
            //
        } else {
            // notify TEAM_TRUSTEES TEAM_FINANCE
            //
        }
    }
}
