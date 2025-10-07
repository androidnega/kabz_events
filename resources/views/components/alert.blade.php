@props(['type' => 'info'])

@php
$types = [
    'success' => 'bg-green-50 border-green-400 text-green-700',
    'error' => 'bg-red-50 border-red-400 text-red-700',
    'warning' => 'bg-yellow-50 border-yellow-400 text-yellow-700',
    'info' => 'bg-blue-50 border-blue-400 text-blue-700',
];

$icons = [
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
];
@endphp

<div {{ $attributes->merge(['class' => 'border rounded-md px-4 py-3 ' . $types[$type]]) }} role="alert">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icons[$type] !!}
            </svg>
        </div>
        <div class="ml-3 flex-1">
            {{ $slot }}
        </div>
    </div>
</div>

