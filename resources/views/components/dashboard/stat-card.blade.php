@props(['title', 'value', 'icon' => 'fa-chart-line', 'color' => 'primary', 'subtitle' => null])

@php
$colorClasses = [
    'primary' => 'bg-purple-50 text-purple-600',
    'blue' => 'bg-blue-50 text-blue-600',
    'green' => 'bg-green-50 text-green-600',
    'yellow' => 'bg-yellow-50 text-yellow-600',
    'red' => 'bg-red-50 text-red-600',
    'indigo' => 'bg-indigo-50 text-indigo-600',
    'teal' => 'bg-teal-50 text-teal-600',
];

$iconColorClass = $colorClasses[$color] ?? $colorClasses['primary'];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600 mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
            @if($subtitle)
                <p class="text-xs text-gray-500 mt-2">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="w-12 h-12 rounded-full {{ $iconColorClass }} flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $icon }} text-xl"></i>
        </div>
    </div>
</div>

