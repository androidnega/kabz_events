@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200';

$variants = [
    'primary' => 'bg-primary text-white hover:bg-purple-700 focus:ring-primary btn-lift',
    'secondary' => 'bg-secondary text-white hover:bg-teal-700 focus:ring-secondary btn-lift',
    'accent' => 'bg-accent text-white hover:bg-amber-600 focus:ring-accent btn-lift',
    'outline' => 'border-2 border-primary text-primary hover:bg-primary hover:text-white focus:ring-primary',
    'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-200',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
];

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
];

$classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>

