<?php

return [

    /*
     * Stripe will sign each webhook using a secret. You can find the used secret at the
     * webhook configuration settings: https://dashboard.stripe.com/account/webhooks.
     */
    'signing_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
     * You can define the job that should be run when a certain webhook hits your application
     * here. The key is the name of the Stripe event type with the `.` replaced by a `_`.
     *
     * You can find a list of Stripe webhook types here:
     * https://stripe.com/docs/api#event_types.
     */
    'jobs' => [
        'charge_succeeded' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeSucceededJob::class,
        'charge_dispute_closed' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeDisputeClosedJob::class,
        'charge_dispute_created' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeDisputeCreatedJob::class,
        'charge_dispute_funds_reinstated' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeDisputeFundsReinstatedJob::class,
        'charge_dispute_funds_withdrawn' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeDisputeFundsWithdrawnJob::class,
        'charge_dispute_updated' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeDisputeUpdatedJob::class,
        'charge_refund_updated' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeRefundUpdatedJob::class,
        'charge_refunded' => \App\Jobs\Banking\Stripe\Webhooks\HandleChargeRefundedJob::class,
    ],

    /*
     * The classname of the model to be used. The class should equal or extend
     * Spatie\StripeWebhooks\ProcessStripeWebhookJob.
     */
    'model' => \Spatie\StripeWebhooks\ProcessStripeWebhookJob::class,
];
