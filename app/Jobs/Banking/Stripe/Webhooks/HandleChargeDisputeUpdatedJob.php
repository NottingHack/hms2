<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

class HandleChargeDisputeUpdatedJob extends EventHandler
{
    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    protected function run()
    {
        // We don't yet have any real need for this event so just log it out for now
        $stripeDispute = $this->stripeEvent->data->object;
        \Log::info('HandleChargeDisputeUpdatedJob');
        \Log::info($stripeDispute);

        return true;
    }
}
