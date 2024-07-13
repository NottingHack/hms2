<?php

namespace App\Jobs\Membership;

use App\Mail\Membership\Retention;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\ProfileRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RetentionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // Nothing to do here
    }

    /**
     * Execute the job.
     *
     * @param ProfileRepository $profileRepository
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(
        ProfileRepository $profileRepository,
        MetaRepository $metaRepository
    ) {
        $targetJoinDate = Carbon::today()->sub(
            CarbonInterval::instance(new \DateInterval($metaRepository->get('membership_retention_email_defer', 'P14D')))
        );

        $profiles = $profileRepository->findByJoinedOn($targetJoinDate);
        foreach ($profiles as $profile) {
            $user = $profile->getUser();
            $to = [
                ['email' => $user->getEmail()],
            ];

            Mail::to($to)->send(new Retention($user));
        }
    }
}
