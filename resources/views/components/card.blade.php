@props(['hoverable' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm ' . ($hoverable ? 'card-hover cursor-pointer' : '')]) }}>
    {{ $slot }}
</div>

