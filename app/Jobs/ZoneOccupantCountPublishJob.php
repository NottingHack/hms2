<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use HMS\Entities\Gatekeeper\Zone;
use karpy47\PhpMqttClient\MQTTClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ZoneOccupantCountPublishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Zone
     */
    protected $zone;

    /**
     * @var int
     */
    protected $occupantCount;

    /**
     * Create a new job instance.
     *
     * @param sting $zone
     * @param int $occupantCount
     *
     * @return void
     */
    public function __construct(Zone $zone, int $occupantCount)
    {
        $this->zone = $zone;
        $this->occupantCount = $occupantCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new MQTTClient(config('services.mqtt.host'), config('services.mqtt.port'));
        $success = $client->sendConnect('hms');  // set your client ID
        if ($success) {
            $client->sendPublish(
                'nh/zone/' . $this->zone->getId() . '/occupantCount',
                $this->occupantCount,
                MQTTClient::MQTT_QOS1,
                1
            );
            $client->sendDisconnect();
        }
        $client->close();
    }
}
