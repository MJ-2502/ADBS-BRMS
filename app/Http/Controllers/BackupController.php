<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Services\ActivityLogger;
use App\Services\BackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct(
        private readonly BackupService $backupService,
        private readonly ActivityLogger $activityLogger
    ) {
    }

    public function index(): View
    {
        return view('backups.index', [
            'backups' => BackupJob::with('operator')->latest()->paginate(15),
        ]);
    }

    public function store(): RedirectResponse
    {
        $job = $this->backupService->run(auth()->user());
        $this->activityLogger->log('backup.created', 'Manual backup executed', ['backup_job_id' => $job->id]);

        return redirect()->route('backups.index')->with('status', 'Backup started.');
    }

    public function download(BackupJob $backup)
    {
        abort_if(!Storage::disk('local')->exists($backup->file_path), 404);

        return Storage::disk('local')->download($backup->file_path);
    }
}
