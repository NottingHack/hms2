<?php

namespace App\Events\Labels;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;

class ManualPrint
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $templateName;

    /**
     * @var array
     */
    public $substitutions;

    /**
     * @var int
     */
    public $copiesToPrint;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        string $templateName,
        $substitutions = [],
        $copiesToPrint = 1
    ) {
        $this->templateName = $templateName;
        $this->substitutions = $substitutions;
        $this->copiesToPrint = $copiesToPrint;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
