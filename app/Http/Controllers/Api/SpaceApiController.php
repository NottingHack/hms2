<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaceApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('feature:space_api');
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $address = config('branding.space_address_1') . ', ';
        $address .= config('branding.space_address_2') . ', ';
        if (config('branding.space_address_3')) {
            $address .= config('branding.space_address_3') . ', ';
        }
        $address .= config('branding.space_city') . ', ';
        if (config('branding.space_county')) {
            $address .= config('branding.space_county') . ', ';
        }
        $address .= config('branding.space_postcode') . ', ';

        $temps = DB::select('CALL sp_get_space_status(@space_open, @last_change)');
        $results = DB::select('SELECT @space_open AS space_open, @last_change AS last_change');
        $spaceOpen = $results[0]->space_open;
        $lastChange = $results[0]->last_change;

        $spaceTemps = [];
        foreach ($temps as $temp) {
            $spaceTemps[] = [
                'value' => floatval($temp->temp),
                'unit' => '°C',
                'location' => $temp->sensor,
            ];
        }

        DB::select('CALL sp_space_net_activity(@status_message)');
        $results = DB::select('SELECT @status_message AS status_message');
        $statusMessage = $results[0]->status_message;

        $spaceApi = [
            'api' => '0.13',
            'api_compatibility' => [
                '14',
            ],

            'space' => config('branding.community_name'),
            'logo' => asset('images/' . config('branding.theme', 'nottinghack') . '/logo.png'),
            'icon' => [
                'open' => asset('images/' . config('branding.theme', 'nottinghack') . '/logo_open.png'),
                'closed' => asset('images/' . config('branding.theme', 'nottinghack') . '/logo_closed.png'),
            ],
            'url' => 'https://' . config('branding.main_domain'),

            'location' => [
                'address' => $address,
                'lat' => config('branding.space_latitude'),
                'lon' => config('branding.space_longitude'),
                'timezone' => 'Europe/London',
            ],

            'contact' => [
                'issue_mail' => base64_encode('realm-admin' . config('branding.email_domain')),
            ],

            'issue_report_channels' => [
                'issue_mail',
            ],

            'spacefed' => [
                'spacenet' => false,
                'spacesaml' => false,
                'spacephone' => false,
            ],

            'state' => [
                'open' => $spaceOpen == 'Yes',
                'lastchange' => intval($lastChange),
                'icon' => [
                    'open' => asset('images/' . config('branding.theme', 'nottinghack') . '/logo_open.png'),
                    'closed' => asset('images/' . config('branding.theme', 'nottinghack') . '/logo_closed.png'),
                ],
                'message' => $statusMessage,
            ],

            'sensors' => [
                'temperature' => $spaceTemps,
            ],
        ];

        if (config('branding.social_networks.twitter.link')) {
            $spaceApi['contact']['twitter'] = config('branding.social_networks.twitter.handle');
        }

        if (config('branding.social_networks.mastodon.link')) {
            $spaceApi['contact']['mastodon'] = config('branding.social_networks.mastodon.handle');
        }

        if (config('branding.social_networks.google_groups.link')) {
            $spaceApi['contact']['ml'] = config('branding.social_networks.google_groups.email');
        }

        if (config('branding.social_networks.facebook.link')) {
            $spaceApi['contact']['facebook'] = config('branding.social_networks.facebook.link');
        }
        if (config('branding.theme') == 'nottinghack') {
            $spaceApi['contact']['irc'] = 'ircs://irc.libera.chat:6697/#nottinghack';
            $spaceApi['spacefed']['spacenet'] = true;

            $spaceApi['feeds'] = [
                'blog' => [
                    'type' => 'rss',
                    'url' => 'http://planet.nottinghack.org.uk/rss20.xml',
                ],
                'calendar' => [
                    'type' => 'ical',
                    'url' => 'https://www.google.com/calendar/ical/info%40nottinghack.org.uk/public/basic.ics',
                ],
            ];
        }

        return response()->json($spaceApi);
    }
}
