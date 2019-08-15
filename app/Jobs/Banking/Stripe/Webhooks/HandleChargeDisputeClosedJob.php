<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

class HandleChargeDisputeClosedJob extends EventHandler
{
    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    protected function run()
    {
        // We don't yet have any real need for this event so just log it out for now
        $stripDispute = $this->stripeEvent->data->object;
        \Log::info('HandleChargeDisputeClosedJob');
        \Log::info($stripDispute);

        return true;
    }
}
