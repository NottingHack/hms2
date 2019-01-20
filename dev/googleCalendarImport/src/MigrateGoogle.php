<?php

namespace LWK\GoogleCalendarImport;

use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Illuminate\Database\Capsule\Manager as Capsule;

class MigrateGoogle
{
    protected $db;

    protected $client;

    protected $calendarIds = [
        1 => 'r46cbjgesib4eotk0qcaomg2gs@group.calendar.google.com',
        3 => 'iag0fej0nqrphfgj1icfhbh968@group.calendar.google.com',
        4 => 'skt2j9tgqsdvgn83t9pcbhrhtc@group.calendar.google.com',
    ];

    public function __construct($path)
    {
        $this->db = new Capsule;

        $this->db->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'hms',
            'username'  => 'hms',
            'password'  => 'secret',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ]);

        $this->db->setAsGlobal();

        $client = new Google_Client();
        $client->setApplicationName('HMS2 Google Calendar Import');
        $client->setAuthConfig("$path/nottinghack-hms-cf36c6256dae.json");
        $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

        $this->client = $client;
    }

    public function handle()
    {
        $startTime = Carbon::now();
        $tools = Capsule::table('tools')->get();

        Capsule::statement('TRUNCATE TABLE bookings;');

        foreach ($tools as $tool) {
            echo "\n---- $tool->name ----\n";
            $toolEvents = $this->getEventsForTool($tool);

            // map events to Bookings
            echo "Mapping Events to Bookings\n";
            $bookings = $this->mapEvents($tool, $toolEvents);

            // Store in the db
            echo "Storing Bookings\n";

            $dataChunks = array_chunk($bookings, 1000, true);
            foreach ($dataChunks as $dataChunk) {
                Capsule::table('bookings')->insert($dataChunk);
            }
        }

        echo $startTime->diff(Carbon::now())->format("Done took: %H:%i:%s\n");

        return 0;
    }

    protected function getEventsForTool($tool)
    {
        $allEvents = [];
        echo "Getting Events for $tool->name\n";
        $service = new Google_Service_Calendar($this->client);
        $optParams = ['maxResults' => 2500];
        $events = $service->events->listEvents($this->calendarIds[$tool->id], $optParams);

        while (true) {
            $allEvents = array_merge($allEvents, $events->getItems());

            echo 'Got ' . count($allEvents) . " Events\n";

            $pageToken = $events->getNextPageToken();
            if ($pageToken) {
                $optParams = ['pageToken' => $pageToken, 'maxResults' => 2500];
                $events = $service->events->listEvents($this->calendarIds[$tool->id], $optParams);
            } else {
                break;
            }
        }
        echo "All events fetched\n";

        return $allEvents;
    }

    protected function mapEvents($tool, $events)
    {
        $bookings = [];

        foreach ($events as $event) {
            $organiser = $event->getOrganizer();
            if ($organiser === null) {
                continue;
            } elseif ($organiser->getEmail() != $this->calendarIds[$tool->id]) {
                // this is not organised by us, probably an invitation
                continue;
            }

            // {"type":"normal","booked":"2014-11-16T09:52:19+00:00","member":"146"}
            $description = json_decode($event->getDescription());

            $bookings[] = [
                'start' => new Carbon($event->getStart()->getDateTime(), 'Europe/London'),
                'end' => new Carbon($event->getEnd()->getDateTime(), 'Europe/London'),
                'type' => strtoupper($description->type),
                'user_id' => (int) $description->member,
                'tool_id' => $tool->id,
                'created_at' => new Carbon($event->getCreated()),
                'updated_at' => new Carbon($event->getUpdated()),
            ];
        }

        return $bookings;
    }
}
