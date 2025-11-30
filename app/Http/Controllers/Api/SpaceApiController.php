<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use HMS\Entities\Role;
use HMS\Repositories\Instrumentation\BarometricPressureRepository;
use HMS\Repositories\Instrumentation\HumidityRepository;
use HMS\Repositories\Instrumentation\TemperatureRepository;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpaceApiController extends Controller
{
    private $temperatureRepository;
    private $humidityRepository;
    private $barometricPressureRepository;
    private $roleRepository;
    private $userRepository;
    private $metaRepository;

    public function __construct(
        TemperatureRepository $temperatureRepository,
        HumidityRepository $humidityRepository,
        BarometricPressureRepository $barometricPressureRepository,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        MetaRepository $metaRepository,
    ) {
        $this->middleware('feature:space_api');

        $this->temperatureRepository = $temperatureRepository;
        $this->humidityRepository = $humidityRepository;
        $this->barometricPressureRepository = $barometricPressureRepository;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
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

        DB::select('CALL sp_get_space_status(@space_open, @last_change)');
        $results = DB::select('SELECT @space_open AS space_open, @last_change AS last_change');
        $spaceOpen = $results[0]->space_open;
        $lastChange = $results[0]->last_change;

        $spaceTemps = [];
        foreach ($this->temperatureRepository->findAll() as $sensor) {
            $spaceTemps[] = [
                'value' => $sensor->getReading(),
                'unit' => '°C',
                'location' => str($sensor->getName())->replace('-LLAP', ''),
            ];
        }
        $spaceHumidities = [];
        foreach ($this->humidityRepository->findAll() as $sensor) {
            $spaceHumidities[] = [
                'value' => $sensor->getReading(),
                'unit' => '%',
                'location' => $sensor->getName(),
            ];
        }
        $spaceBarometricPressures = [];
        foreach ($this->barometricPressureRepository->findAll() as $sensor) {
            $spaceBarometricPressures[] = [
                'value' => $sensor->getReading(),
                'unit' => 'hPa',
                'location' => $sensor->getName(),
            ];
        }

        $memberCounts = [];
        foreach (Role::MEMBER_ROLES as $roleName) {
            if (in_array($roleName, [Role::MEMBER_TEMPORARYBANNED, Role::MEMBER_BANNED])) {
                continue;
            }

            $memberCounts[] = [
                'value' => $this->userRepository->countMembersByRoleName($roleName),
                'name' => $this->roleRepository->findOneByName($roleName)->getDisplayName(),
            ];
        }

        $minimumMembershipFee = $this->metaRepository->getInt('membership_minimum_amount', 500) / 100;
        $recommendedMembershipFee = $this->metaRepository->getInt('membership_recommended_amount', 1500) / 100;

        DB::select('CALL sp_space_net_activity(@status_message)');
        $results = DB::select('SELECT @status_message AS status_message');
        $statusMessage = $results[0]->status_message;

        $spaceApi = [
            'api' => '0.13',
            'api_compatibility' => [
                '15',
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
                'humidity' => $spaceHumidities,
                'barometer' => $spaceBarometricPressures,
                'total_member_count' => $memberCounts,
            ],
            'membership_plans' => [
                [
                    'name' => 'Minimum',
                    'value' => $minimumMembershipFee,
                    'currency' => 'GBP',
                    'description' => 'Minimum required to grant membership',
                    'billing_interval' => 'monthly',
                ],
                [
                    'name' => 'Recommended',
                    'value' => $recommendedMembershipFee,
                    'currency' => 'GBP',
                    'description' => 'Recommended amount of £' . $recommendedMembershipFee . ' or more per month',
                    'billing_interval' => 'monthly',
                ],
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
