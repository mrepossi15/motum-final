@props(['type' => 'submit', 'color' => 'orange'])

<button
    type="{{ $type }}"
    class="px-4 py-2 rounded-lg text-white bg-{{ $color }}-500 hover:bg-{{ $color }}-600 focus:outline-none focus:ring-2 focus:ring-{{ $color }}-300"
    {{ $attributes }}
>
    {{ $slot }}
</button>