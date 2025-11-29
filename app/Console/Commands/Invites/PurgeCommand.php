<?php

namespace App\Console\Commands\Invites;

use Carbon\Carbon;
use DateInterval;
use HMS\Repositories\InviteRepository;
use HMS\Repositories\MetaRepository;
use Illuminate\Console\Command;

class PurgeCommand extends Command
{
    /**
     * @var InviteRepository
     */
    protected $inviteRepository;

    /**
     * @var MetaRepository
     */
    protected $metaRepository;

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
     * @param InviteRepository $inviteRepository
     * @param MetaRepository $metaRepository
     */
    public function __construct(InviteRepository $inviteRepository, MetaRepository $metaRepository)
    {
        parent::__construct();
        $this->inviteRepository = $inviteRepository;
        $this->metaRepository = $metaRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (is_null($this->argument('date'))) {
            try {
                if ($this->metaRepository->has('purge_cutoff_interval')) {
                    $interval = $this->metaRepository->get('purge_cutoff_interval');
                    $date = Carbon::now()
                        ->sub(new DateInterval($interval));
                } else {
                    $date = Carbon::now()->subMonths(6);
                }
            } catch (\Exception $e) {
                $date = Carbon::now()->subMonths(6);
            }
        } else {
            $date = Carbon::createFromFormat('Y-m-d', $this->argument('date'));
        }

        $this->inviteRepository->removeAllOlderThan($date);

        $this->info('Invites older than ' . $date->format('Y-m-d') . ' removed.');
    }
}
