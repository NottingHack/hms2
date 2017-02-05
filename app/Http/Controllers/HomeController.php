<?php

namespace App\Http\Controllers;

use HMS\Repositories\MetaRepository;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
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
}
