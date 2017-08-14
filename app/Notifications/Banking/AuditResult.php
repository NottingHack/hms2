<?php

namespace App\Notifications\Banking;

use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use HMS\Repositories\GateKeeper\PinRepository;
use Illuminate\Notifications\Messages\MailMessage;
use HMS\Repositories\GateKeeper\AccessLogRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

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
     * @return void
     */
    public function __construct($approveUsers,
        $warnUsers,
        $revokeUsers,
        $reinstateUsers,
        $paymentNotificationsClearCount,
        AccessLogRepository $accessLogRepository,
        BankTransactionRepository $bankTransactionRepository,
        PinRepository $pinRepository,
        RoleUpdateRepository $roleUpdateRepository)
    {
        $this->paymentNotificationsClearCount = $paymentNotificationsClearCount;

        $this->formattedApproveUsers = [];
        foreach ($approveUsers as $user) {
            $this->formattedApproveUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'pin' => $pinRepository->findByUser($user)[0]->getPin(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
            ];
        }

        $this->formattedWarnUsers = [];
        foreach ($warnUsers as $user) {
            $accessLog = $accessLogRepository->findLatestByUser($user);
            if ( ! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateTimeString();
            } else {
                $lastAccess = 'Never Visited';
            }

            $this->formattedWarnUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => 'TODO',
                'lastPaymentDate' => $bankTransactionRepository->findLatestTransactionByAccount($user->getAccount())->getTransactionDate()->toDateTimeString(),
                'lastVisitDate' => $lastAccess,
            ];
        }

        $this->formattedRevokeUsers = [];
        foreach ($revokeUsers as $user) {
            $accessLog = $accessLogRepository->findLatestByUser($user);
            if ( ! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateTimeString();
            } else {
                $lastAccess = 'Never Visited';
            }

            $this->formattedRevokeUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => 'TODO',
                'lastPaymentDate' => $bankTransactionRepository->findLatestTransactionByAccount($user->getAccount())->getTransactionDate()->toDateTimeString(),
                'lastVisitDate' => $lastAccess,
            ];
        }

        $this->formattedReinstateUsers = [];
        foreach ($reinstateUsers as $user) {
            $accessLog = $accessLogRepository->findLatestByUser($user);
            if ( ! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateTimeString();
            } else {
                $lastAccess = 'Never Visited';
            }

            $this->formattedReinstateUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => 'TODO',
                'dateMadeExMember' => $roleUpdateRepository->find($user)->getCreatedAt()->toDateTimeString(),
                'lastVisitDate' => $lastAccess,
            ];
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Audit results')
                                ->markdown('emails.banking.auditresults',
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->to($notifiable->getSlackChannel())
            ->attachment(function ($attachment) use ($userId) {
                $attachment->title('Membership Auidt Results')
                            ->fields([
                                'New Members' => count($this->formattedApprovedUsers),
                                'Notified Members' => count($this->formattedWarnUsers),
                                'Revoked Members' => count($this->formattedRevokeUsers),
                                'Reinstated Members' => count($this->formattedReinstateUsers),
                                ])
                            ->timestamp(Carbon::now());
            });
    }
}
