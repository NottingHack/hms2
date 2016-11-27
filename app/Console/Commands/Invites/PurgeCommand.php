<?php

namespace App\Console\Commands\Invites;

use Carbon\Carbon;
use Illuminate\Console\Command;
use HMS\Repositories\InviteRepository;
use Doctrine\Common\Collections\Criteria;

class PurgeCommand extends Command
{
    /**
     * @var HMS\Repositories\InviteRepository
     */
    protected $inviteRepository;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:purge
                            {date? : Cutoff date for purge (YYYY-MM-DD). Default 6 months}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge invites older than 6 months or given date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(InviteRepository $inviteRepository)
    {
        parent::__construct();
        $this->inviteRepository = $inviteRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (is_null($this->argument('date'))) {
            $date = Carbon::now()->subMonths(6);
        } else {
            $date = Carbon::createFromFormat('Y-m-d', $this->argument('date'));
        }
        $this->inviteRepository->removeAllOlderThan($date);

        // \EntityManager::remove($invites);
    }
}
