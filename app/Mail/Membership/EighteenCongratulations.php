<?php

namespace App\Mail\Membership;

use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EighteenCongratulations extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $fullname;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->fullname = $user->getFullname();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('branding.space_name') . ': Happy 18th Birthday')
                    ->markdown('emails.membership.eighteenCongratulations');
    }
}
