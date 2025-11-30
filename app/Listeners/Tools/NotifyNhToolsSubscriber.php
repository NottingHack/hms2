<?php

namespace App\Listeners\Tools;

use App\Events\Tools\BookingCancelled;
use App\Events\Tools\BookingChanged;
use App\Events\Tools\NewBooking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use karpy47\PhpMqttClient\MQTTClient;

class NotifyNhToolsSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @param NewBooking $event
     */
    public function newBooking(NewBooking $event)
    {
        $this->poll($event->booking->getTool()->getName());
    }

    /**
     * @param BookingChanged $event
     */
    public function bookingChanged(BookingChanged $event)
    {
        $this->poll($event->booking->getTool()->getName());
    }

    /**
     * @param BookingCancelled $event
     */
    public function bookingCancelled(BookingCancelled $event)
    {
        $this->poll($event->tool->getName());
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        return [
            NewBooking::class => 'newBooking',
            BookingChanged::class => 'bookingChanged',
            BookingCancelled::class => 'bookingCancelled',
        ];
    }

    /**
     * Publish a Poll to mqtt.
     *
     * @param string $toolName
     */
    protected function poll(string $toolName)
    {
        $client = new MQTTClient(config('services.mqtt.host'), config('services.mqtt.port'));
        $success = $client->sendConnect('hms');  // set your client ID
        if ($success) {
            $client->sendPublish('nh/bookings/poll', $toolName);
            $client->sendDisconnect();
        }
        $client->close();
    }
}
