@props(['type' => 'default'])

@php
$types = [
    'default' => 'bg-gray-100 text-gray-800',
    'primary' => 'bg-purple-100 text-primary',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-800',
    'info' => 'bg-blue-100 text-blue-800',
    'verified' => 'bg-green-100 text-green-800',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $types[$type]]) }}>
    {{ $slot }}
</span>

