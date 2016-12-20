<?php

namespace App\Mail;

use HMS\Entities\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterestRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Invite
     */
    protected $invite;

    /**
     * Create a new message instance.
     *
     * @param Invite $invite
     */
    public function __construct(Invite $invite)
    {
        //
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.interestregistered')
                    ->text('emails.text.interestregistered-plain')
                    ->with([
                        'token' => $this->invite->getInviteToken(),
                        'membershipEmail' => 'membership@nottinghack.org.uk',
                        'trusteesEmail' => 'trustees@notinghack.org.uk',
                        'groupLink' => 'https://groups.google.com/group/nottinghack?hl=en',
                        'rulesHTML' => 'http://rules.nottinghack.org.uk',
                    ]);
    }
}
