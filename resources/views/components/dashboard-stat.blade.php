@props(['label' => 'Metric', 'value' => 0, 'icon' => null, 'trend' => null, 'color' => 'blue'])

@php
$colorClasses = [
    'blue' => 'bg-blue-50 text-blue-600',
    'green' => 'bg-emerald-50 text-emerald-600',
    'purple' => 'bg-purple-50 text-purple-600',
    'orange' => 'bg-orange-50 text-orange-600',
];
@endphp

<div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-lg hover:border-slate-300">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-3xl font-bold text-slate-900 transition-transform duration-300 group-hover:scale-105">{{ number_format($value) }}</p>
            @if($trend)
                <div class="mt-2 flex items-center text-xs">
                    @if($trend > 0)
                        <svg class="mr-1 h-3 w-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold text-emerald-600">+{{ $trend }}%</span>
                    @else
                        <svg class="mr-1 h-3 w-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-semibold text-rose-600">{{ $trend }}%</span>
                    @endif
                    <span class="ml-1 text-slate-500">vs last month</span>
                </div>
            @endif
        </div>
        @if($icon)
            <div class="rounded-xl p-3 {{ $colorClasses[$color] ?? $colorClasses['blue'] }} transition-transform duration-300 group-hover:scale-110">
                {!! $icon !!}
            </div>
        @endif
    </div>
    <div class="absolute bottom-0 left-0 h-1 w-full bg-linear-to-r from-slate-100 to-slate-200 transition-all duration-300 group-hover:from-{{ $color }}-400 group-hover:to-{{ $color }}-500"></div>
</div>
