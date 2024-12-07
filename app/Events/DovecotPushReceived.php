<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DovecotPushReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param string $user
     * @param string $folder
     * @param string $event
     * @param string $from
     * @param string $subject
     * @param string $snippet
     * @param int $messages
     * @param int $unseen
     */
    public function __construct(
        public string $user,
        public string $folder,
        public string $event,
        public string $from,
        public string $subject,
        public string $snippet,
        public int $messages,
        public int $unseen,
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
