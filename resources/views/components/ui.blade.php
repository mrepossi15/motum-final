<!-- resources/views/components/ui.blade.php -->

@props(['type' => 'primary', 'href' => '#', 'disabled' => false])

@php
    $baseClasses = 'px-4 py-2 rounded-md transition focus:outline-none';
    $colorClasses = match($type) {
        'primary' => 'bg-orange-500 text-white hover:bg-orange-600',
        'success' => 'bg-green-500 text-white hover:bg-green-600',
        'warning' => 'bg-yellow-500 text-black hover:bg-yellow-600',
        'danger' => 'bg-red-500 text-white hover:bg-red-600',
        'secondary' => 'bg-gray-300 text-black hover:bg-gray-400',
        'outline' => 'border border-orange-500 text-orange-500 hover:bg-orange-100',
        default => 'bg-gray-500 text-white',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $baseClasses }} {{ $colorClasses }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
        {{ $slot }}
    </a>
@else
    <button {{ $disabled ? 'disabled' : '' }} class="{{ $baseClasses }} {{ $colorClasses }}">
        {{ $slot }}
    </button>
@endif