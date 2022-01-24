<?php

namespace App\Jobs;

use App\Mail\ToCurrentMembers;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Hydrator\NoopHydrator;
use Mailgun\Mailgun;
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
     * @param CssToInlineStyles $cssToInlineStyles
     * @param ViewFactory $viewFactory
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        CssToInlineStyles $cssToInlineStyles,
        ViewFactory $viewFactory,
        RoleRepository $roleRepository
    ) {
        $trusteesRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesEmail = $trusteesRole->getEmail();
        $trusteesDisplayName = $trusteesRole->getDisplayName();

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
                $viewFactory->make('vendor.mail.html.themes.' . config('mail.markdown.theme', 'default'))->render()
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
                    'full_name' => 'Trustees',
                ],
            ];
        } else {
            // send to all current members
            // this converts to an Illuminate\Support\Collection first then maps our User object
            $to = collect($currentMembers)->mapWithKeys(function ($user) {
                return [$user->getEmail() => [
                    'full_name' => $user->getFirstname(),
                ],
                ];
            })->toArray();
        }

        $trusteesMgEmail = preg_replace('/@/m', '@mg.', $trusteesEmail);

        // Build the Mailgun service
        if (Str::contains(config('services.mailgun.endpoint'), 'bin.mailgun.net')) {
            $configurator = (new HttpClientConfigurator())
                ->setEndpoint(config('services.mailgun.endpoint'))
                ->setApiKey(config('services.mailgun.secret'))
                ->setDebug(true);

            $mailgun = new Mailgun($configurator, new NoopHydrator());
        } else {
            $mailgun = Mailgun::create(
                config('services.mailgun.secret'),
                'https://' . config('services.mailgun.endpoint')
            );
        }

        $batchMessage = $mailgun->messages()->getBatchMessage(
            config('services.mailgun.domain')
        );

        $batchMessage
            ->setOpenTracking(true)
            ->setSubject($this->subject)
            ->addCustomHeader('sender', $trusteesDisplayName . ' <' . $trusteesMgEmail . '>')
            ->setReplyToAddress($trusteesEmail, ['full_name' => $trusteesDisplayName])
            ->setFromAddress($trusteesMgEmail, ['full_name' => $trusteesDisplayName]);

        $batchMessage->setHtmlBody($viewFactory->make($views['html'], $data)->render());
        $batchMessage->setTextBody($viewFactory->make($views['text'], $data)->render());

        foreach ($to as $email => $details) {
            $batchMessage->addToRecipient($email, $details);
        }

        $batchMessage->finalize();

        LogSentMailgunMessageJob::dispatch(
            $to,
            $subject,
            $data['htmlContent'],
            implode(',', $batchMessage->getMessageIds())
        );
    }
}
