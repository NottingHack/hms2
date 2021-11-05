<?php

namespace App\Mail;

use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Soundasleep\Html2Text;

class ToCurrentMembers extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email subject.
     *
     * @var string
     */
    public $subject;

    /**
     * The email content as text/html.
     *
     * @var string
     */
    public $htmlContent;

    /**
     * The email content as text/plain.
     *
     * @var string
     */
    public $textPlain;

    /**
     * Create a new message instance.
     *
     * @param string $subject The email subject.
     * @param string $htmlContent The email content as text/html.
     */
    public function __construct(string $subject, string $htmlContent)
    {
        $this->subject = $subject;
        $this->htmlContent = $htmlContent;
        $this->textPlain = Html2Text::convert($htmlContent);

        //TODO: for hms2 pull trustee address from Role::TEAM_TRUSTEES
    }

    /**
     * Build the message.
     *
     * @param RoleRepository $roleRepository
     *
     * @return $this
     */
    public function build(RoleRepository $roleRepository)
    {
        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesTeamRole->getEmail();

        return $this->from($trusteesEmail, $trusteesTeamRole->getDisplayName())
            ->subject($this->subject)
            ->view('emails.emailMembers.toCurrentMembers')
            ->text('emails.emailMembers.toCurrentMembers_plain');
    }

    /**
     * Render the template into text.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function renderText()
    {
        Container::getInstance()->call([$this, 'build']);

        return Container::getInstance()->make('mailer')->render(
            'emails.emailMembers.toCurrentMembers_plain',
            $this->buildViewData()
        );
    }
}
