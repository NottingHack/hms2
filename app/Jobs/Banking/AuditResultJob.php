<?php

namespace App\Jobs\Banking;

use HMS\Entities\Role;
use HMS\Entities\User;
use Illuminate\Bus\Queueable;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\Banking\AuditResult;
use HMS\Repositories\RoleUpdateRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use HMS\Repositories\GateKeeper\AccessLogRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

class AuditResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User[]
     */
    protected $approveUsers;

    /**
     * @var User[]
     */
    protected $warnUsers;

    /**
     * @var User[]
     */
    protected $revokeUsers;

    /**
     * @var User[]
     */
    protected $reinstateUsers;

    /**
     * @var int
     */
    protected $paymentNotificationsClearCount;

    /**
     * Create a new job instance.
     *
     * @param User[] $approveUsers
     * @param User[] $warnUsers
     * @param User[] $revokeUsers
     * @param User[] $reinstateUsers
     * @param int    $paymentNotificationsClearCount
     *
     * @return void
     */
    public function __construct(
        $approveUsers,
        $warnUsers,
        $revokeUsers,
        $reinstateUsers,
        int $paymentNotificationsClearCount
    ) {
        $this->approveUsers = $approveUsers;
        $this->warnUsers = $warnUsers;
        $this->revokeUsers = $revokeUsers;
        $this->reinstateUsers = $reinstateUsers;
        $this->paymentNotificationsClearCount = $paymentNotificationsClearCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        AccessLogRepository $accessLogRepository,
        BankTransactionRepository $bankTransactionRepository,
        UserRepository $userRepository,
        RoleUpdateRepository $roleUpdateRepository,
        RoleRepository $roleRepository
    ) {
        $formattedApproveUsers = [];
        foreach ($this->approveUsers as $user) {
            // grab fresh user.
            $user = $userRepository->findOneById($user->getId());

            $formattedApproveUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'pin' => $user->getPin() ? $user->getPin()->getPin() : 'No Pin yet',
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
            ];
        }

        $formattedWarnUsers = [];
        foreach ($this->warnUsers as $user) {
            // grab fresh user.
            $user = $userRepository->findOneById($user->getId());

            $accessLog = $accessLogRepository->findLatestByUser($user);
            if (! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateString();
            } else {
                $lastAccess = 'Never Visited';
            }

            $formattedWarnUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => $user->getProfile()->getBalance(),
                'lastPaymentDate' => $bankTransactionRepository
                    ->findLatestTransactionByAccount($user->getAccount())
                    ->getTransactionDate()
                    ->toDateString(),
                'lastVisitDate' => $lastAccess,
            ];
        }

        $formattedRevokeUsers = [];
        foreach ($this->revokeUsers as $user) {
            // grab fresh user.
            $user = $userRepository->findOneById($user->getId());

            $accessLog = $accessLogRepository->findLatestByUser($user);
            if (! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateString();
            } else {
                $lastAccess = 'Never Visited';
            }

            $formattedRevokeUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => $user->getProfile()->getBalance(),
                'lastPaymentDate' => $bankTransactionRepository
                    ->findLatestTransactionByAccount($user->getAccount())
                    ->getTransactionDate()
                    ->toDateString(),
                'lastVisitDate' => $lastAccess,
            ];
        }

        $formattedReinstateUsers = [];
        foreach ($this->reinstateUsers as $user) {
            // grab fresh user.
            $user = $userRepository->findOneById($user->getId());

            $accessLog = $accessLogRepository->findLatestByUser($user);
            if (! is_null($accessLog)) {
                $lastAccess = $accessLog->getAccessTime()->toDateString();
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

            $formattedReinstateUsers[] = [
                'id' => $user->getId(),
                'fullName' => $user->getFullname(),
                'email' => $user->getEmail(),
                'jointAccount' => count($user->getAccount()->getUsers()) > 1 ? 'yes' : 'no',
                'balance' => $user->getProfile()->getBalance(),
                'dateMadeExMember' => $dateMadeExMember,
                'lastVisitDate' => $lastAccess,
            ];
        }

        // now email the audit results
        $auditResultNotification = new AuditResult(
            $formattedApproveUsers,
            $formattedWarnUsers,
            $formattedRevokeUsers,
            $formattedReinstateUsers,
            $this->paymentNotificationsClearCount
        );

        $membershipTeamRole = $roleRepository->findOneByName(Role::TEAM_MEMBERSHIP);
        $membershipTeamRole->notify($auditResultNotification);

        $trusteesTeamRole = $roleRepository->findOneByName(Role::TEAM_TRUSTEES);
        $trusteesTeamRole->notify($auditResultNotification);
    }
}
