<?php

namespace App\Http\Controllers;

use HMS\Entities\Role;
use HMS\Entities\Tools\Booking;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\Members\BoxRepository;
use HMS\Repositories\Tools\BookingRepository;
use HMS\Repositories\Members\ProjectRepository;
use HMS\Repositories\Snackspace\TransactionRepository;

class HomeController extends Controller
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
     * Create a new controller instance.
     *
     * @param ProjectRepository $projectRepository
     * @param BoxRepository $boxRepository
     * @param TransactionRepository $transactionRepository
     * @param RoleRepository $roleRepository
     * @param BookingRepository $bookingRepository
     *
     * @return void
     */
    public function __construct(
        ProjectRepository $projectRepository,
        BoxRepository $boxRepository,
        TransactionRepository $transactionRepository,
        RoleRepository $roleRepository,
        BookingRepository $bookingRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->boxRepository = $boxRepository;
        $this->transactionRepository = $transactionRepository;
        $this->roleRepository = $roleRepository;
        $this->bookingRepository = $bookingRepository;
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
     * Show the application welcome screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        if (\Auth::check()) {
            return redirect()->route('home');
        }

        return view('welcome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        if ($user->hasRoleByName(Role::MEMBER_APPROVAL)) {
            return view('pages.awaitingApproval');
        } elseif ($user->hasRoleByName(Role::MEMBER_PAYMENT)) {
            return view('pages.awaitingPayment');
        }

        $projects = $this->projectRepository->paginateByUser($user, 5, 'page', true);
        $boxCount = $this->boxRepository->countInUseByUser($user);
        $snackspaceTransactions = $this->transactionRepository->paginateByUser($user, 3);
        $teams = $this->roleRepository->findTeamsForUser($user);
        $bookings = $this->bookingRepository->findFutureByUser($user);
        $mappedBookings = array_map([$this, 'mapBookings'], $bookings);

        return view('home')->with([
            'user' => $user,
            'projects' => $projects,
            'boxCount' => $boxCount,
            'snackspaceTransactions' => $snackspaceTransactions,
            'teams' => $teams,
            'bookings' => $mappedBookings,
        ]);
    }
}
