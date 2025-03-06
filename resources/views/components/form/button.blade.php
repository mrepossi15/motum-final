@props(['type' => 'submit', 'color' => 'orange'])

<button
    type="{{ $type }}"
   class="bg-orange-500 text-white text-md px-6 py-3 rounded-md w-full hover:bg-orange-600 transition"
    {{ $attributes }}
>
    {{ $slot }}
</button>

