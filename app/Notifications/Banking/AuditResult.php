<?php

namespace App\Notifications\Banking;

use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use HMS\Repositories\GateKeeper\PinRepository;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
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
     * @param User[]                    $approveUsers
     * @param User[]                    $warnUsers
     * @param User[]                    $revokeUsers
     * @param User[]                    $reinstateUsers
     * @param int                       $paymentNotificationsClearCount
     * @param AccessLogRepository       $accessLogRepository
     * @param BankTransactionRepository $bankTransactionRepository
     * @param PinRepository             $pinRepository
     * @param RoleUpdateRepository      $roleUpdateRepository
     * @param RoleRepository            $roleRepository
     */
    public function __construct(
        $approveUsers,
        $warnUsers,
        $revokeUsers,
        $reinstateUsers,
        $paymentNotificationsClearCount,
        AccessLogRepository $accessLogRepository,
        BankTransactionRepository $bankTransactionRepository,
        PinRepository $pinRepository,
        RoleUpdateRepository $roleUpdateRepository,
        RoleRepository $roleRepository
    ) {
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
            if (! is_null($accessLog)) {
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
                'lastPaymentDate' => $bankTransactionRepository
                    ->findLatestTransactionByAccount($user->getAccount())
                    ->getTransactionDate()
                    ->toDateTimeString(),
                'lastVisitDate' => $lastAccess,
            ];
        }

        $this->formattedRevokeUsers = [];
        foreach ($revokeUsers as $user) {
            $accessLog = $accessLogRepository->findLatestByUser($user);
            if (! is_null($accessLog)) {
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
                'lastPaymentDate' => $bankTransactionRepository
                    ->findLatestTransactionByAccount($user->getAccount())
                    ->getTransactionDate()
                    ->toDateString(),
                'lastVisitDate' => $lastAccess,
            ];
        }

        $this->formattedReinstateUsers = [];
        foreach ($reinstateUsers as $user) {
            $accessLog = $accessLogRepository->findLatestByUser($user);
            if (! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateTimeString();
            } else {
                $lastAccess = 'Never Visited';
            }

            $exRole = $roleRepository->findOneByName(Role::MEMBER_EX);
            $madeExRoleUpdate = $roleUpdateRepository->findLatestRoleAddedByUser($exRole, $user);
            if (is_null($madeExRoleUpdate)) {
                // crap, should not get here.
                $dateMadeExMember = 'Never, Tell the Software team';
            } else {
                $dateMadeExMember = $madeExRoleUpdate->getCreatedAt()->toDateString();
            }

            $this->formattedReinstateUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => 'TODO',
                'dateMadeExMember' => $dateMadeExMember,
                'lastVisitDate' => $lastAccess,
            ];
        }
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
        return ['mail', 'slack'];
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
                $attachment->title('Membership Auidt Results')
                            ->fields([
                                'New Members' => $approveCount,
                                'Notified Members' => $warnCount,
                                'Revoked Members' => $revokeCount,
                                'Reinstated Members' => $reinstateCount,
                                ])
                            ->timestamp(Carbon::now());
            });
    }
}
