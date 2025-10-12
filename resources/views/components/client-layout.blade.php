{{-- Alias for client layout --}}
<x-dynamic-component :component="'layouts.client'">
    @isset($header)
        <x-slot name="header">
            {{ $header }}
        </x-slot>
    @endisset

    {{ $slot }}
</x-dynamic-component>

