<?php

namespace App\Console\Commands;

use App\Models\Repository;
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
    public function handle(): int
    {
        $repos = Repository::all();

        foreach ($repos as $repo) {
            $localDir = storage_path('app/repos/' . $repo->title);

            // Ensure the local directory exists
            if (!is_dir($localDir)) {
                mkdir($localDir, 0777, true);
            }

            // Clone the repository
            $this->info("Cloning repository from $repo->repoUrl...");
            exec("git clone $repo->repoUrl $localDir", $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error("Failed to clone repository: " . implode("\n", $output));
                return 1;
            }

            $destination = env('REPOS_DESTINATION');

            // Transfer the cloned repository to the destination server
            $this->info("Transferring repository to $destination...");
            $transferCommand = "rsync --progress --delete -e 'ssh -p23' --recursive $localDir $destination";
            exec($transferCommand, $output, $returnVar);

            if ($returnVar !== 0) {
                $this->error("Failed to transfer repository: " . implode("\n", $output));
                return 1;
            }

            $this->info('Repository cloned and transferred successfully.');

            // Clean up the local directory
            exec("rm -rf $localDir");
        }

        return 0;
    }
}
