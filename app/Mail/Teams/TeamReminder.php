<?php

namespace App\Mail\Teams;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $teamName;

    /**
     * Create a new message instance.
     *
     * @param string $teamName
     *
     * @return void
     */
    public function __construct(string $teamName)
    {
        $this->teamName = $teamName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $month = Carbon::now()->addDays(8)->format('F');

        return $this->from('trustees@mg.nottinghack.org.uk', 'Nottingham Hackspace Trustees')
            ->replyTo('trustees@nottinghack.org.uk', 'Nottingham Hackspace Trustees')
            ->subject('Team Reminder: ' . $month . ' Members Meeting Update')
            ->markdown('emails.teams.reminder');
    }
}
