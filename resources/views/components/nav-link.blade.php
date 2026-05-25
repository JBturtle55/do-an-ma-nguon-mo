@props(['active' => false, 'href' => '#'])

@php
$classes = $active
    ? 'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white'
    : 'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-150';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
