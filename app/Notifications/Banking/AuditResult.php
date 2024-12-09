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
use Illuminate\Support\Facades\View;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use App\Notifications\DiscordNotificationSensitivity;
use App\Notifications\NotificationSensitivityType;

class AuditResult extends Notification implements ShouldQueue, DiscordNotificationSensitivity
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
     * @var array
     */
    protected $formattedAwaitingUsersUnderMinimum;
    /**
     * @var array
     */
    protected $formattedWarnUsersMinimumAmount;
    /**
     * @var array
     */
    protected $formattedRevokeUsersMinimumAmount;
    /**
     * @var array
     */
    protected $formattedExUsersUnderMinimum;

    /**
     * Create a new notification instance.
     *
     * @param User[] $formatedApproveUsers
     * @param User[] $formatedWarnUsers
     * @param User[] $formatedRevokeUsers
     * @param User[] $formatedReinstateUsers
     * @param int    $paymentNotificationsClearCount
     * @param User[] $formattedAwaitingUsersUnderMinimum
     * @param User[] $formattedWarnUsersMinimumAmount
     * @param User[] $formattedRevokeUsersMinimumAmount
     * @param User[] $formattedExUsersUnderMinimum
     */
    public function __construct(
        $formatedApproveUsers,
        $formatedWarnUsers,
        $formatedRevokeUsers,
        $formatedReinstateUsers,
        $paymentNotificationsClearCount,
        $formattedAwaitingUsersUnderMinimum,
        $formattedWarnUsersMinimumAmount,
        $formattedRevokeUsersMinimumAmount,
        $formattedExUsersUnderMinimum
    ) {
        $this->formattedApproveUsers = $formatedApproveUsers;
        $this->formattedWarnUsers = $formatedWarnUsers;
        $this->formattedRevokeUsers = $formatedRevokeUsers;
        $this->formattedReinstateUsers = $formatedReinstateUsers;
        $this->paymentNotificationsClearCount = $paymentNotificationsClearCount;
        $this->formattedAwaitingUsersUnderMinimum = $formattedAwaitingUsersUnderMinimum;
        $this->formattedWarnUsersMinimumAmount = $formattedWarnUsersMinimumAmount;
        $this->formattedRevokeUsersMinimumAmount = $formattedRevokeUsersMinimumAmount;
        $this->formattedExUsersUnderMinimum = $formattedExUsersUnderMinimum;
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
            $channels[] = DiscordChannel::class;
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
        $theme = config('mail.markdown.theme');
        $themeWide = $theme . '-wide';

        return (new MailMessage)
            ->theme(View::exists('vendor.mail.html.themes.' . $themeWide) ? $themeWide : $theme)
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
                    'formattedAwaitingUsersUnderMinimum' => $this->formattedAwaitingUsersUnderMinimum,
                    'formattedWarnUsersMinimumAmount' => $this->formattedWarnUsersMinimumAmount,
                    'formattedRevokeUsersMinimumAmount' => $this->formattedRevokeUsersMinimumAmount,
                    'formattedExUsersUnderMinimum' => $this->formattedExUsersUnderMinimum,
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
        $warnCount = count($this->formattedWarnUsers) + count($this->formattedWarnUsersMinimumAmount);
        $revokeCount = count($this->formattedRevokeUsers) + count($this->formattedRevokeUsersMinimumAmount);
        $reinstateCount = count($this->formattedReinstateUsers) + count($this->formattedExUsersUnderMinimum);

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


    /**
     * Returns the sensitivity for notification routing to
     * Discord. e.g. whether it should go to the private or public
     * team channel.
     *
     * @return string
     */
    public function getDiscordSensitivity()
    {
        return NotificationSensitivityType::PUBLIC;
    }
}
