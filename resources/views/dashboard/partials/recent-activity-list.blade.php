<div class="space-y-2 sm:space-y-3 text-sm">
    @forelse($recentActivities as $log)
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 sm:px-4 sm:py-3 dark:border-slate-700 dark:bg-slate-900/30">
            <p class="font-medium text-slate-800 dark:text-white">{{ $log->event }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $log->created_at->diffForHumans() }} • {{ $log->user?->name }}</p>
            @if($log->description)
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $log->description }}</p>
            @endif
        </div>
    @empty
        <p class="text-sm text-slate-500 dark:text-slate-400">No activity yet.</p>
    @endforelse
</div>
