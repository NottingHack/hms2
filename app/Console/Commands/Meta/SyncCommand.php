<?php

namespace App\Console\Commands\Meta;

use HMS\Repositories\MetaRepository;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meta:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync meta with any missing entries from the default config';

    /**
     *  @var MetaRepository
     */
    protected $metaRepository;

    /**
     * Create a new command instance.
     *
     * @param MetaRepository $metaRepository
     *
     * @return void
     */
    public function __construct(MetaRepository $metaRepository)
    {
        parent::__construct();

        $this->metaRepository = $metaRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $defaults = config('meta.meta');

        foreach ($defaults as $key => $value) {
            if (! $this->metaRepository->has($key)) {
                $this->metaRepository->set($key, $value);
                $this->info('Created meta: ' . $key);
            }
        }
    }
}
