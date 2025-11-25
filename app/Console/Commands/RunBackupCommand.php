<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\BackupService;
use Illuminate\Console\Command;

class RunBackupCommand extends Command
{
    protected $signature = 'brms:backup {--user= : Operator user ID for audit trail}';

    protected $description = 'Run a BRMS data backup snapshot';

    public function __construct(private readonly BackupService $backupService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $userId = $this->option('user');
        $user = $userId ? User::find($userId) : null;

        $job = $this->backupService->run($user);

        $this->info("Backup created at {$job->file_path}");

        return self::SUCCESS;
    }
}
