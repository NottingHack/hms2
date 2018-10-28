<?php

namespace App\Http\Controllers;

use HMS\Repositories\MetaRepository;
use HMS\Repositories\Members\ProjectRepository;

class HomeController extends Controller
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProjectRepository $projectRepository
     *
     * @return void
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->middleware('auth');
        $this->projectRepository = $projectRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        $projectCount = $this->projectRepository->countActiveByUser($user);

        return view('home')->with([
            'user' => $user,
            'projectCount' => $projectCount,
        ]);
    }

    /**
     * Show the Hacksapce access codes.
     *
     * @return \Illuminate\Http\Response
     */
    public function accessCodes(MetaRepository $metaRepository)
    {
        if ( ! \Gate::allows('accessCodes.view')) {
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
