@props(['type' => 'info'])

@php
    $colors = [
        'info' => 'blue',
        'success' => 'green',
        'error' => 'red',
        'warning' => 'yellow',
    ];
@endphp

@if (session($type))
    <div class="bg-{{ $colors[$type] }}-100 border-l-4 border-{{ $colors[$type] }}-500 text-{{ $colors[$type] }}-700 p-4 mb-4 rounded-lg">
        {{ session($type) }}
    </div>
@endif