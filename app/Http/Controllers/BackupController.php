<?php

namespace App\Http\Controllers;

use App\Models\BackupJob;
use App\Services\ActivityLogger;
use App\Services\BackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

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

    public function download(BackupJob $backup): StreamedResponse
    {
        abort_if(!Storage::disk('local')->exists($backup->file_path), 404);

        return Storage::disk('local')->download($backup->file_path);
    }

    public function restoreFromJob(BackupJob $backup): RedirectResponse
    {
        abort_if($backup->status !== 'completed', 404);

        try {
            $result = $this->backupService->restoreFromJob($backup, auth()->user());
            $this->activityLogger->log('backup.restored.job', 'Backup restored from stored archive', [
                'backup_job_id' => $backup->id,
                'source' => $result['source'],
            ]);

            return redirect()->route('backups.index')->with('status', 'Backup restored from ' . basename($result['source']) . '.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'restore' => 'Unable to restore backup: ' . $exception->getMessage(),
            ]);
        }
    }

    public function restoreFromUpload(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'backup_file' => ['required', 'file', 'mimetypes:application/json,text/plain', 'max:5120'],
        ]);

        try {
            $result = $this->backupService->restoreFromUploadedFile($data['backup_file'], auth()->user());
            $this->activityLogger->log('backup.restored.upload', 'Backup restored from uploaded file', [
                'source' => $result['source'],
            ]);

            return redirect()->route('backups.index')->with('status', 'Backup restored from uploaded file.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'backup_file' => 'Unable to restore backup: ' . $exception->getMessage(),
            ]);
        }
    }
}
