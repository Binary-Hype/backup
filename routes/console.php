<?php

use App\Console\Commands\BackupRepos;
use Illuminate\Support\Facades\Schedule;

Schedule::command(BackupRepos::class)->everyFiveMinutes();
