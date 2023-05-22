<?php

namespace App\Notifications\Banking;

use Carbon\Carbon;
use HMS\Entities\Role;
use HMS\Entities\User;
use HMS\Helpers\Discord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class AuditResult extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var array
     */
    protected $formattedApproveUsers;

    /**
     * @var array
     */
    protected $formattedWarnUsers;

    /**
     * @var array
     */
    protected $formattedRevokeUsers;

    /**
     * @var array
     */
    protected $formattedReinstateUsers;

    /**
     * @var int
     */
    protected $paymentNotificationsClearCount;

    /**
     * Create a new notification instance.
     *
     * @param User[]                    $formatedApproveUsers
     * @param User[]                    $formatedWarnUsers
     * @param User[]                    $formatedRevokeUsers
     * @param User[]                    $formatedReinstateUsers
     * @param int                       $paymentNotificationsClearCount
     */
    public function __construct(
        $formatedApproveUsers,
        $formatedWarnUsers,
        $formatedRevokeUsers,
        $formatedReinstateUsers,
        $paymentNotificationsClearCount
    ) {
        $this->formattedApproveUsers = $formatedApproveUsers;
        $this->formattedWarnUsers = $formatedWarnUsers;
        $this->formattedRevokeUsers = $formatedRevokeUsers;
        $this->formattedReinstateUsers = $formatedReinstateUsers;
        $this->paymentNotificationsClearCount = $paymentNotificationsClearCount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['mail', 'slack'];
        if (config('services.discord.token')) {
            array_push($channels, DiscordChannel::class);
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('HMS Audit results')
            ->markdown(
                'emails.banking.auditResults',
                [
                    'teamName' => ($notifiable instanceof Role) ? $notifiable->getDisplayName() : $notifiable->getName(),
                    'formattedApproveUsers' => $this->formattedApproveUsers,
                    'formattedWarnUsers' => $this->formattedWarnUsers,
                    'formattedRevokeUsers' => $this->formattedRevokeUsers,
                    'formattedReinstateUsers' => $this->formattedReinstateUsers,
                    'paymentNotificationsClearCount' => $this->paymentNotificationsClearCount,
                ]
            );
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $approveCount = count($this->formattedApproveUsers);
        $warnCount = count($this->formattedWarnUsers);
        $revokeCount = count($this->formattedRevokeUsers);
        $reinstateCount = count($this->formattedReinstateUsers);

        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(function ($attachment) use ($approveCount, $warnCount, $revokeCount, $reinstateCount) {
                $attachment->title('Membership Audit Results')
                            ->fields([
                                'New Members' => $approveCount,
                                'Notified Members' => $warnCount,
                                'Revoked Members' => $revokeCount,
                                'Reinstated Members' => $reinstateCount,
                            ])
                            ->timestamp(Carbon::now());
            });
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return NotificationChannels\Discord\DiscordMessage
     */
    public function toDiscord($notifiable)
    {
        $approveCount = count($this->formattedApproveUsers);
        $warnCount = count($this->formattedWarnUsers);
        $revokeCount = count($this->formattedRevokeUsers);
        $reinstateCount = count($this->formattedReinstateUsers);

        $embed = [
            'title' => 'Membership Audit Results',
            'fields' => [
                [
                    'name' => 'New Members',
                    'value' => $approveCount,
                ],
                [
                    'name' => 'Notified Members',
                    'value' => $warnCount,
                ],
                [
                    'name' => 'Revoked Members',
                    'value' => $revokeCount,
                ],
                [
                    'name' => 'Reinstated Members',
                    'value' => $reinstateCount,
                ],
            ],
        ];

        return (new DiscordMessage())->embed($embed);
    }
}
