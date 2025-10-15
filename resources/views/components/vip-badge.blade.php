@props(['tier' => null, 'size' => 'md'])

@php
$tierColors = [
    'Bronze' => 'bg-amber-100 text-amber-800 border-amber-300',
    'Silver' => 'bg-slate-100 text-slate-800 border-slate-300',
    'Gold' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
    'Platinum' => 'bg-purple-100 text-purple-800 border-purple-300',
];

$tierIcons = [
    'Bronze' => 'fa-medal',
    'Silver' => 'fa-award',
    'Gold' => 'fa-crown',
    'Platinum' => 'fa-gem',
];

$sizeClasses = [
    'sm' => 'text-xs px-2 py-0.5',
    'md' => 'text-sm px-2.5 py-1',
    'lg' => 'text-base px-3 py-1.5',
];

$iconSizes = [
    'sm' => 'text-xs',
    'md' => 'text-sm',
    'lg' => 'text-base',
];

// Determine tier from string (e.g., "VIP Gold" -> "Gold")
$cleanTier = null;
if ($tier) {
    foreach (array_keys($tierColors) as $tierName) {
        if (str_contains($tier, $tierName)) {
            $cleanTier = $tierName;
            break;
        }
    }
}

$colorClass = $tierColors[$cleanTier] ?? 'bg-purple-100 text-purple-800 border-purple-300';
$icon = $tierIcons[$cleanTier] ?? 'fa-star';
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
$iconSize = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

@if($tier && $cleanTier)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center font-semibold rounded-full border ' . $colorClass . ' ' . $sizeClass]) }}>
        <i class="fas {{ $icon }} mr-1 {{ $iconSize }}"></i>
        <span>VIP {{ $cleanTier }}</span>
    </span>
@endif

