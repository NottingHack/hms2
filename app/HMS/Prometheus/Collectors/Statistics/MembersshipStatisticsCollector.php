<?php

namespace HMS\Prometheus\Collectors\Statistics;

use HMS\Entities\Role;
use HMS\Governance\VotingManager;
use HMS\Repositories\RoleRepository;
use HMS\Repositories\UserRepository;
use Spatie\Prometheus\Collectors\Collector;
use Spatie\Prometheus\Facades\Prometheus;

class MembersshipStatisticsCollector implements Collector
{
    public function register(): void
    {
        $userRepository = app(UserRepository::class);
        $roleRepository = app(RoleRepository::class);

        Prometheus::addGauge('Member count')
            ->name('statistics_member_count')
            ->label('status')
            ->helpText('Count of Members')
            ->value(function () use ($userRepository, $roleRepository) {
                $memberCounts = [];
                foreach (Role::MEMBER_ROLES as $roleName) {
                    if (in_array($roleName, [Role::MEMBER_TEMPORARYBANNED, Role::MEMBER_BANNED])) {
                        continue;
                    }

                    $memberCounts[] = [
                        $userRepository->countMembersByRoleName($roleName),
                        [$roleRepository->findOneByName($roleName)->getDisplayName()],
                    ];
                }

                return $memberCounts;
            });

        $votingManager = app(VotingManager::class);

        Prometheus::addGauge('Voting Members')
            ->name('statistics_voting_member_count')
            ->helpText('Count Voting of Members')
            ->value(fn () => $votingManager->countVotingMembers());
    }
}
