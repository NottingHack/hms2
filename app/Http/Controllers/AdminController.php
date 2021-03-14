<?php

namespace App\Http\Controllers;

use HMS\Entities\User;
use HMS\Governance\VotingManager;
use HMS\Repositories\Banking\BankTransactionRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\Members\ProjectRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\Tools\UsageRepository;

class AdminController extends Controller
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var BoxRepository
     */
    protected $boxRepository;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var BookingRepository
     */
    protected $bookingRepository;

    /**
     * @var ToolRepository
     */
    protected $toolRepository;

    /**
     * @var BankTransactionRepository
     */
    protected $bankTransactionRepository;

    /**
     * @var VotingManager
     */
    protected $votingManager;

    /**
     * @var UsageRepository
     */
    protected $toolUsageRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProjectRepository $projectRepository
     * @param BoxRepository $boxRepository
     * @param TransactionRepository $transactionRepository
     * @param RoleRepository $roleRepository
     * @param BookingRepository $bookingRepository
     * @param ToolRepository $toolRepository
     * @param BankTransactionRepository $bankTransactionRepository
     * @param VotingManager $votingManager
     * @param UsageRepository $toolUsageRepository
     *
     * @return void
     */
    public function __construct(
        ProjectRepository $projectRepository,
        BoxRepository $boxRepository,
        TransactionRepository $transactionRepository,
        RoleRepository $roleRepository,
        BookingRepository $bookingRepository,
        ToolRepository $toolRepository,
        BankTransactionRepository $bankTransactionRepository,
        VotingManager $votingManager,
        UsageRepository $toolUsageRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->boxRepository = $boxRepository;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;
        $this->bookingRepository = $bookingRepository;
        $this->toolRepository = $toolRepository;
        $this->toolUsageRepository = $toolUsageRepository;
        $this->bankTransactionRepository = $bankTransactionRepository;
        $this->votingManager = $votingManager;

        $this->middleware('canAny:profile.view.limited,profile.view.all')->only(['userOverview']);
    }

    /**
     * Overview page of a given user.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function userOverview(User $user)
    {
        $projects = $this->projectRepository->paginateByUser($user, 5, 'page', true);
        $boxCount = $this->boxRepository->countInUseByUser($user);
        $snackspaceTransactions = $this->transactionRepository->paginateByUser($user, 3);
        $teams = $this->roleRepository->findTeamsForUser($user);
        $bookings = $this->bookingRepository->findFutureByUser($user);

        $tools = $this->toolRepository->findAll();
        $toolIds = array_map(function ($tool) {
            return $tool->getId();
        }, $tools);
        $toolsFreeTime = [];
        foreach ($tools as $tool) {
            $toolsFreeTime[$tool->getId()] = $this->toolUsageRepository->freeTimeForToolUser($tool, $user);
        }

        $memberStatus = $this->roleRepository->findMemberStatusForUser($user);

        $bankTransactions = [];
        if ($user->getAccount()) {
            $bankTransactions = $this->bankTransactionRepository->paginateByAccount($user->getAccount(), 3);
        }

        $votingStatus = $this->votingManager->getVotingStatusForUser($user);

        return view('admin.user_overview')->with([
            'user' => $user,
            'projects' => $projects,
            'boxCount' => $boxCount,
            'snackspaceTransactions' => $snackspaceTransactions,
            'teams' => $teams,
            'bookings' => $bookings,
            'tools' => $tools,
            'toolIds' => $toolIds,
            'toolsFreeTime' => $toolsFreeTime,
            'memberStatus' => $memberStatus,
            'bankTransactions' => $bankTransactions,
            'votingStatus' => $votingStatus,
        ]);
    }
}
