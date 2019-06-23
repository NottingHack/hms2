<?php

namespace App\Http\Controllers;

use HMS\Entities\User;
use HMS\Entities\Invite;
use HMS\Entities\Tools\Booking;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Tools\ToolRepository;
use HMS\Repositories\Members\BoxRepository;
use App\Events\MembershipInterestRegistered;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Repositories\Members\ProjectRepository;
use HMS\Repositories\Snackspace\TransactionRepository;
use HMS\Repositories\Banking\BankTransactionRepository;

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
     * Create a new controller instance.
     *
     * @param ProjectRepository $projectRepository
     * @param BoxRepository $boxRepository
     * @param TransactionRepository $transactionRepository
     * @param RoleRepository $roleRepository
     * @param BookingRepository $bookingRepository
     * @param ToolRepository $toolRepository
     * @param BankTransactionRepository $bankTransactionRepository
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
        BankTransactionRepository $bankTransactionRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->boxRepository = $boxRepository;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;
        $this->bookingRepository = $bookingRepository;
        $this->toolRepository = $toolRepository;
        $this->bankTransactionRepository = $bankTransactionRepository;

        $this->middleware('canOr:profile.view.limited,profile.view.all')->only(['userOverview']);
        $this->middleware('can:search.invites')->only(['invitesResend']);
    }

    /**
     * Helper for array_map to prepare bookings for BookingCalendarList.vue.
     *
     * @param Booking $booking
     *
     * @return array
     */
    protected function mapBookings(Booking $booking)
    {
        // TODO: swap out for Fractal
        return [
            'id' => $booking->getId(),
            'start' => $booking->getStart()->toAtomString(),
            'end' => $booking->getEnd()->toAtomString(),
            'title' => $booking->getTool()->getName(),
            'type' => $booking->getType(),
            'toolId' => $booking->getTool()->getId(),
        ];
    }

    /**
     * Admin home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin()
    {
        return view('pages.admin');
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
        $mappedBookings = array_map([$this, 'mapBookings'], $bookings);
        $tools = $this->toolRepository->findAll();
        $memberStatus = $this->roleRepository->findMemberStatusForUser($user);
        $bankTransactions = $this->bankTransactionRepository->paginateByAccount($user->getAccount(), 3);

        return view('admin.user_overview')->with([
            'user' => $user,
            'projects' => $projects,
            'boxCount' => $boxCount,
            'snackspaceTransactions' => $snackspaceTransactions,
            'teams' => $teams,
            'bookings' => $mappedBookings,
            'tools' => $tools,
            'memberStatus' => $memberStatus,
            'bankTransactions' => $bankTransactions,
        ]);
    }

    /**
     * Resend an invite.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function invitesResend(Invite $invite)
    {
        event(new MembershipInterestRegistered($invite));

        flash('Invite resent.')->success();

        return back();
    }
}
