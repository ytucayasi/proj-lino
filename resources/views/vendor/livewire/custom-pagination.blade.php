{{-- resources/views/vendor/livewire/custom-pagination.blade.php --}}
@if ($paginator->hasPages())
    <div class="w-100 d-flex mt-3 justify-content-center"
        data-anime='{ "translateY": [0, 0], "opacity": [0,1], "duration": 600, "delay":100, "staggervalue": 150, "easing": "easeOutQuad" }'>
        <ul class="pagination pagination-style-01 fs-13 fw-500 mb-0">
            {{-- Flecha Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item">
                    <span class="page-link">
                        <i class="feather icon-feather-arrow-left fs-18 d-xs-none"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <button wire:click="previousPage('page')" class="page-link">
                        <i class="feather icon-feather-arrow-left fs-18 d-xs-none"></i>
                    </button>
                </li>
            @endif

            {{-- Números de Página --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    {{-- Separador "..." --}}
                    <li class="page-item">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $page === $paginator->currentPage() ? 'active' : '' }}">
                            <button wire:click="gotoPage({{ $page }}, 'page')" class="page-link">
                                {{ sprintf("%02d", $page) }}
                            </button>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Flecha Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button wire:click="nextPage('page')" class="page-link">
                        <i class="feather icon-feather-arrow-right fs-18 d-xs-none"></i>
                    </button>
                </li>
            @else
                <li class="page-item">
                    <span class="page-link">
                        <i class="feather icon-feather-arrow-right fs-18 d-xs-none"></i>
                    </span>
                </li>
            @endif
        </ul>
    </div>
@endif