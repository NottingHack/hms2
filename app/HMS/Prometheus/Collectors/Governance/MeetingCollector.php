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
            ->helpText('Meeting stats: quorum, attendees, proxies and absentees')
            ->value(fn () => app()->call([$this, 'getValueAttendeeCount']));
    }

    public function getValueAttendeeCount(
        MeetingRepository $meetingRepository,
        ProxyRepository $proxyRepository
    ) {
        $meetings = $meetingRepository->paginateAll();
        $meetingCounts = [];

        foreach ($meetings as $meeting) {
            $meetingCounts[] = [
                $meeting->getAttendees()->count(),
                [$meeting->getTitle(), 'attendees'],
            ];

            $meetingCounts[] = [
                $meeting->getProxies()->count(),
                [$meeting->getTitle(), 'proxies'],
            ];

            $meetingCounts[] = [
                $meeting->getAbsentees()->count(),
                [$meeting->getTitle(), 'absentees'],
            ];

            $meetingCounts[] = [
                $meeting->getQuorum(),
                [$meeting->getTitle(), 'quorum'],
            ];
        }

        return $meetingCounts;
    }
}
