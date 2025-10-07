@props(['title', 'value', 'icon', 'color' => 'indigo'])

@php
$colorClasses = [
    'indigo' => 'bg-indigo-500',
    'yellow' => 'bg-yellow-500',
    'green' => 'bg-green-500',
    'orange' => 'bg-orange-500',
    'purple' => 'bg-purple-500',
    'teal' => 'bg-teal-500',
    'red' => 'bg-red-500',
];
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 {{ $colorClasses[$color] ?? 'bg-indigo-500' }} rounded-md p-3">
                {{ $icon }}
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $value }}
                        </div>
                        @isset($subtitle)
                        <span class="ml-2 text-sm text-gray-500">
                            {{ $subtitle }}
                        </span>
                        @endisset
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

