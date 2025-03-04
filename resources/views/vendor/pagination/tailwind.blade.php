@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4">
        <ul class="inline-flex items-center -space-x-px">
            {{-- Link Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="px-3 py-2 ml-0 leading-tight text-gray-400 border border-gray-300 rounded-l-lg cursor-not-allowed">
                    Anterior
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" 
                       class="px-3 py-2 ml-0 leading-tight text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100">
                        Anterior
                    </a>
                </li>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="px-3 py-2 leading-tight text-gray-500 border border-gray-300">
                        {{ $element }}
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li aria-current="page">
                                <span class="px-3 py-2 leading-tight text-white bg-orange-500 border border-orange-500 rounded">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" 
                                   class="px-3 py-2 leading-tight text-gray-700 bg-white border border-gray-300 hover:bg-gray-100">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Link Siguiente --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" 
                       class="px-3 py-2 leading-tight text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100">
                        Siguiente
                    </a>
                </li>
            @else
                <li class="px-3 py-2 leading-tight text-gray-400 border border-gray-300 rounded-r-lg cursor-not-allowed">
                    Siguiente
                </li>
            @endif
        </ul>
    </nav>
@endif
