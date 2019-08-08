<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use DateTimeZone;
use Stripe\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\WebhookClient\Models\WebhookCall;
use HMS\Repositories\Banking\Stripe\ChargeRepository;

class HandleChargeDisputeCreatedJob implements ShouldQueue
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
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        ChargeRepository $chargeRepository,
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

        $charge->setDisputeId($stripeDispute->id);
        $chargeRepository->save($charge);

        // evidenceDueBy in our timezone
        $evidenceDueBy = Carbon::createFromTimestamp($stripeDispute->evidence_details->due_by)
            ->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $reason = $stripeDispute->reason;

        // notify TEAM_TRUSTEES TEAM_FINANCE
    }
}
