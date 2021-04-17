<?php

namespace App\Jobs;

use App\Mail\ToCurrentMembers;
use Bogardo\Mailgun\Contracts\Mailgun;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;
use Soundasleep\Html2Text;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class EmailCurrentMembersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Should this only sent as a test.
     *
     * @var bool
     */
    public $testSend;

    /**
     * Create a new job instance.
     *
     * @param string $subject The email subject.
     * @param string $htmlContent The email content as text/html.
     * @param bool $testSend Should this only sent as a test.
     */
    public function __construct(
        string $subject,
        string $htmlContent,
        bool $testSend = true
    ) {
        $this->subject = $subject;
        $this->htmlContent = $htmlContent;
        $this->testSend = $testSend;
    }

    /**
     * Execute the job.
     *
     * @param Mailgun $mailgun
     * @param CssToInlineStyles $cssToInlineStyles
     * @param ViewFactory $viewFactory
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        Mailgun $mailgun,
        CssToInlineStyles $cssToInlineStyles,
        ViewFactory $viewFactory,
        RoleRepository $roleRepository
    ) {
        $trusteesRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesRole->getEmail();

        $currentMembers = $roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers();

        // Send using Mailgun
        $views = [
            'html' => 'emails.emailMembers.toCurrentMembers_mailgun',
            'text' => 'emails.emailMembers.toCurrentMembers_plain',
        ];

        $emailView = new ToCurrentMembers($this->subject, $this->htmlContent);
        $renderedHtml = $emailView->render();
        $renderedHtmlCSS = new HtmlString(
            $cssToInlineStyles->convert(
                $renderedHtml,
                $viewFactory->make('vendor.mail.html.themes.default')->render()
            )
        );

        $data = [
            'htmlContent' => $renderedHtmlCSS,
            'textPlain' => Html2Text::convert($this->htmlContent),
        ];

        if ($this->testSend) {
            // only send to the trustees address
            $to = [
                $trusteesEmail => [
                    'name' => 'Trustees',
                ],
            ];
        } else {
            // send to all current members
            // this converts to an Illuminate\Support\Collection first then maps our User object
            $to = collect($currentMembers)->mapWithKeys(function ($user) {
                return [$user->getEmail() => [
                    'name' => $user->getFirstname(),
                ],
                ];
            })->toArray();
        }

        $trusteesMgEmail = preg_replace('/@/m', '@mg.', $trusteesEmail);
        $subject = $this->subject;
        $response = $mailgun->send(
            $views,
            $data,
            function ($message) use ($subject, $trusteesEmail, $trusteesMgEmail, $to) {
                $message
                    ->trackOpens(true)
                    ->subject($subject)
                    ->header('sender', $trusteesRole->getDisplayName() . ' <' . $trusteesMgEmail . '>')
                    ->replyTo($trusteesEmail, $trusteesRole->getDisplayName())
                    ->from($trusteesMgEmail, $trusteesRole->getDisplayName())
                    ->to($to);
            }
        );

        LogSentMailgunMessageJob::dispatch($to, $subject, $data['htmlContent'], $response->id);
    }
}
