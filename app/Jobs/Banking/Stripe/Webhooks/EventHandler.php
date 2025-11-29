<?php

namespace App\Jobs\Banking\Stripe\Webhooks;

use Carbon\Carbon;
use HMS\Entities\Banking\Stripe\Event;
use HMS\Factories\Banking\Stripe\ChargeFactory;
use HMS\Factories\Snackspace\TransactionFactory;
use HMS\Repositories\Banking\Stripe\ChargeRepository;
use HMS\Repositories\Banking\Stripe\EventRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Models\WebhookCall;
use Stripe\Event as StripeEvent;

abstract class EventHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebhookCall
     */
    protected $webhookCall;

    /**
     * @var StripeEvent
     */
    protected $stripeEvent;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ChargeRepository
     */
    protected $chargeRepository;

    /**
     * @var ChargeFactory
     */
    protected $chargeFactory;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

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
     * @param EventRepository $eventRepository
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
        EventRepository $eventRepository,
        TransactionFactory $transactionFactory,
        TransactionRepository $transactionRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->chargeFactory = $chargeFactory;
        $this->chargeRepository = $chargeRepository;
        $this->chargeFactory = $chargeFactory;
        $this->eventRepository = $eventRepository;
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;

        $this->stripeEvent = StripeEvent::constructFrom($this->webhookCall->payload);

        // in order to not reprocess an event multiple times we log it to the db
        $event = $this->eventRepository->findOneById($this->stripeEvent->id);

        if (! is_null($event)) {
            if (! is_null($event->getHandledAt())) {
                // this stripeEvent id has already been handled before
                // we should get out now
                Log::info(
                    'Stripe Event ' . $this->stripeEvent->id .
                    ' already handled at ' . $event->getHandledAt()->toDateTimeString()
                );

                return;
            }
        // event is in the db but not handled yet, must have failed before :/
        } else {
            // not seen this stripeEvent id before
            $event = new Event();
            $event->setId($this->stripeEvent->id);
            $event = $this->eventRepository->save($event);
        }

        // deal with this parenthetical stripeEvent as needed
        $complete = $this->run();

        if ($complete) {
            // Log this event as now being handled
            // we will not get here if run returns false or an exception was thrown
            $event->setHandledAt(Carbon::now());
            $this->eventRepository->save($event);
        }
    }

    /**
     * Handle this event.
     *
     * @return bool Is this event handling complete.
     */
    abstract protected function run();
}
