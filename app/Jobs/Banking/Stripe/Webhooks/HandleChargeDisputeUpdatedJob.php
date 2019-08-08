<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use Stripe\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\WebhookClient\Models\WebhookCall;
use HMS\Repositories\Banking\Stripe\ChargeRepository;

class HandleChargeDisputeUpdatedJob implements ShouldQueue
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
     *
     * @return void
     */
    public function handle(
        ChargeRepository $chargeRepository
    ) {
        $event = Event::constructFrom($this->webhookCall->payload);
        $stripeDispute = $event->data->object;
        \Log::info('HandleChargeDisputeUpdatedJob');
        \Log::info($stripeDispute);
    }
}
