<?php

namespace App\Jobs;

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
     * List of shell command to run pre Artisan.
     *
     * @var array
     */
    private $preShellCommands = [
        'git submodule update --recursive',
        'composer install',
        'npm ci',
        'npm run production',
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
        'config:cache',
        'route:cache',
        'view:cache',
        'doctrine:clear:metadata:cache',
        'doctrine:clear:query:cache',
        'doctrine:clear:result:cache',
        'doctrine:generate:proxies',
        'permissions:sync',
        'queue:restart'
    ];

    /**
     * List of shell command to run post Artisan.
     *
     * @var array
     */
    private $postShellCommands = [
        //
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RoleRepository $roleRepository)
    {
        $commandResults = array();
        \Artisan::call('down');

        foreach ($this->preShellCommands as $shellCommand) {
            \Log::debug('PostGitDeployedJob: Running pre command:  ' . $shellCommand);
            array_push($commandResults, $this->callShellCommand($shellCommand));
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

            array_push($commandResults, [
                    'cmd' => $artisanCommand,
                    'output' => $output,
                    'return_code' => $exitCode,
                ]);
        }

        foreach ($this->postShellCommands as $shellCommand) {
            \Log::debug('PostGitDeployedJob: Running post command:  ' . $shellCommand);
            array_push($commandResults, $this->callShellCommand($shellCommand));
        }

        \Artisan::call('up');
        $softwareTeamRole = $roleRepository->findOneByName(Role::TEAM_SOFTWARE);
        $softwareTeamRole->notify(new PostGitDeployedResults($commandResults));
    }

    protected function callShellCommand(string $command)
    {
        $output = array();
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
}
