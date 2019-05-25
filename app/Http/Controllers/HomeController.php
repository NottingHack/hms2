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

    /**
     * Show the Hacksapce access codes.
     *
     * @return \Illuminate\Http\Response
     */
    public function accessCodes(MetaRepository $metaRepository)
    {
        if (! \Gate::allows('accessCodes.view')) {
            return redirect()->route('home');
        }
        $accessCodes = [
            'outerDoorCode' => $metaRepository->get('access_street_door'),
            'innerDoorCode' => $metaRepository->get('access_inner_door'),
            'wifiSsid' => $metaRepository->get('access_wifi_ssid'),
            'wifiPass' => $metaRepository->get('access_wifi_password'),
            'guestWifiSsid' => $metaRepository->get('access_guest_wifi_ssid'),
            'guestWifiPass' => $metaRepository->get('access_guest_wifi_password'),
        ];

        return view('pages.access')->with($accessCodes);
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
}
