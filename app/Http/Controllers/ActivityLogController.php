<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        return view('activity-logs.index', [
            'logs' => ActivityLog::with('user')->latest()->paginate(20),
        ]);
    }
}
