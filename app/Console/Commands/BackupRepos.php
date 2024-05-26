<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class BackupRepos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repos:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone the Git repositories and transfer them to another server via SSH';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $repoTitle = 'rezept-roulette.com';
        $repoUrl = "git@github.com:Binary-Hype/recipe-roulette.git";
        $destinationUser =  env('REPOS_DESTIONATION_USER');
        $destinationServer = env('REPOS_DESTIONATION_SERVER');
        $destinationPath = env('REPOS_DESTIONATION_PATH');
        $localDir = storage_path('app/repos/' . $repoTitle);


        // Ensure the local directory exists
        if (!is_dir($localDir)) {
            mkdir($localDir, 0777, true);
        }

        // Clone the repository
        $this->info("Cloning repository from $repoUrl...");
        exec("git clone $repoUrl $localDir", $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Failed to clone repository: " . implode("\n", $output));
            return 1;
        }

        // Transfer the cloned repository to the destination server
        $this->info("Transferring repository to $destinationServer:$destinationPath...");
        $transferCommand = "rsync --progress --delete -e 'ssh -p23' --recursive $localDir $destinationUser@$destinationServer:$destinationPath";
        exec($transferCommand, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Failed to transfer repository: " . implode("\n", $output));
            return 1;
        }

        $this->info('Repository cloned and transferred successfully.');

        // Clean up the local directory
        exec("rm -rf $localDir");

        return 0;
    }
}
