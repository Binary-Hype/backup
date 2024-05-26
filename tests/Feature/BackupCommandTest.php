<?php

namespace Tests\Feature;

use App\Models\Repository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_command_success(): void
    {
        $repo = Repository::create([
           'title' => 'automatic-backup',
           'repoUrl' => 'git@github.com:Binary-Hype/backup.git'
        ]);

        $destination = $this->createTempDirectory('destination');
        $this->setEnvironmentVariable('REPOS_DESTINATION', $destination);

        $this->artisan('repos:backup', [])->assertExitCode(0);

        $this->assertDirectoryExists($destination . DIRECTORY_SEPARATOR . $repo->title);
        $this->assertDirectoryDoesNotExist('storage/app/repos/' . $repo->title);

        File::deleteDirectory($destination);
    }

    protected function createTempDirectory($prefix): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . '_' . uniqid();
        File::makeDirectory($path);
        return $path;
    }

    protected function setEnvironmentVariable($key, $value): void
    {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
