<?php

namespace App\Jobs;

use Exception;
use Carbon\Carbon;
use HMS\Entities\Role;
use Illuminate\Bus\Queueable;
use HMS\Repositories\RoleRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\PostGitDeployedResults;

class PostGitDeployedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * List of shell command to run pre Artisan.
     *
     * @var array
     */
    private $preShellCommands = [
        'git submodule update --recursive',
        'git describe --always --tags --dirty >version',
        'composer install --no-interaction --optimize-autoloader',
        'npm ci',
    ];

    /**
     * List of Artisan commands to run.
     *
     * @var array
     */
    private $artisanCommands = [
        'migrate --force',
        'doctrine:migrations:migrate --force',
        'hms:database:refresh-views',
        'hms:database:refresh-procedures',
        'cache:clear',
        'config:cache',
        'route:cache',
        'view:cache',
        'auth:clear-resets',
        'doctrine:clear:metadata:cache',
        'doctrine:clear:query:cache',
        'doctrine:clear:result:cache',
        'doctrine:generate:proxies',
        'permissions:sync',
        'horizon:terminate',
        'ziggy:generate "resources/js/ziggy.js"',
    ];

    /**
     * List of shell command to run post Artisan.
     *
     * @var array
     */
    private $postShellCommands = [
        'npm run production',
    ];

    /**
     * @var Carbon
     */
    private $startTime;

    /**
     * @var array
     */
    private $commandResults = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queue = 'maintenance';
    }

    /**
     * Execute the job.
     *
     * @param RoleRepository $roleRepository
     *
     * @return void
     */
    public function handle(RoleRepository $roleRepository)
    {
        $this->startTime = Carbon::now();
        \Artisan::call('down');

        foreach ($this->preShellCommands as $shellCommand) {
            \Log::debug('PostGitDeployedJob: Running pre command:  ' . $shellCommand);
            array_push($this->commandResults, $this->callShellCommand($shellCommand));
        }

        foreach ($this->artisanCommands as $artisanCommand) {
            \Log::debug('PostGitDeployedJob: Running artisan command: ' . $artisanCommand);
            try {
                $exitCode = \Artisan::call($artisanCommand);
                $output = \Artisan::output();
            } catch (\Exception $e) {
                $output = $e->getMessage();
                $exitCode = -1;
            }

            array_push($this->commandResults, [
                'cmd' => $artisanCommand,
                'output' => $output,
                'return_code' => $exitCode,
            ]);
        }

        foreach ($this->postShellCommands as $shellCommand) {
            \Log::debug('PostGitDeployedJob: Running post command:  ' . $shellCommand);
            array_push($this->commandResults, $this->callShellCommand($shellCommand));
        }

        \Artisan::call('up');
        $stopTime = Carbon::now();

        $softwareTeamRole = $roleRepository->findOneByName(Role::TEAM_SOFTWARE);
        $softwareTeamRole->notify(new PostGitDeployedResults(true, $this->commandResults, $this->startTime, $stopTime));
    }

    /**
     * Helper for calling exec.
     *
     * @param string $command
     *
     * @return array
     */
    protected function callShellCommand(string $command)
    {
        $output = [];
        $returnCode = '';
        $cmd = escapeshellcmd('cd')
                . ' '
                . escapeshellarg(base_path())
                . ' ; '
                . escapeshellcmd($command)
                . ' 2>&1';

        exec($cmd, $output, $returnCode);

        return [
            'cmd' => $cmd,
            'output' => implode("\n", $output) . "\n",
            'return_code' => $returnCode,
        ];
    }

    /**
     * The job failed to process.
     *
     * @param Exception $exception
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        \Artisan::call('up');
        \Log::warning('PostGitDeployedJob: Job failed');
        // Send user notification of failure, etc...
    }
}
