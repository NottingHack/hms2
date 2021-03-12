<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

class HandleChargeRefundUpdatedJob extends EventHandler
{
    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    protected function run()
    {
        // We don't yet have any real need for this event so just log it out for now
        $stripeRefund = $this->stripeEvent->data->object;
        \Log::info('HandleChargeRefundUpdatedJob');
        \Log::info($stripeRefund);

        return true;
    }
}
