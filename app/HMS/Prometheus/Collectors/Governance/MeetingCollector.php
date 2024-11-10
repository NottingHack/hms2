<?php

namespace HMS\Prometheus\Collectors\Governance;

use HMS\Repositories\Governance\MeetingRepository;
use HMS\Repositories\Governance\ProxyRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class MeetingCollector implements Collector
{
    public function register(): void
    {
        Prometheus::addGauge('Meeting Counts')
            ->name('statistics_meeting_counts')
            ->labels(['meeting', 'type'])
            ->helpText('Meeting stats: quorum, attendees, proxies, absentees, represented_proxies, checked_in')
            ->value(fn () => app()->call([$this, 'getValueAttendeeCount']));
    }

    public function getValueAttendeeCount(
        MeetingRepository $meetingRepository,
        ProxyRepository $proxyRepository
    ) {
        $meetings = $meetingRepository->paginateAll();
        $values = [];

        foreach ($meetings as $meeting) {
            $representedProxies = $proxyRepository->countRepresentedForMeeting($meeting);
            $attendees = $meeting->getAttendees()->count();
            $checkInCount = $representedProxies + $attendees;

            $values[] = [
                $attendees,
                [$meeting->getTitle(), 'attendees'],
            ];

            $values[] = [
                $meeting->getProxies()->count(),
                [$meeting->getTitle(), 'proxies'],
            ];

            $values[] = [
                $representedProxies,
                [$meeting->getTitle(), 'represented_proxies'],
            ];

            $values[] = [
                $meeting->getAbsentees()->count(),
                [$meeting->getTitle(), 'absentees'],
            ];

            $values[] = [
                $meeting->getQuorum(),
                [$meeting->getTitle(), 'quorum'],
            ];

            $values[] = [
                $checkInCount,
                [$meeting->getTitle(), 'checked_in'],
            ];
        }

        return $values;
    }
}
