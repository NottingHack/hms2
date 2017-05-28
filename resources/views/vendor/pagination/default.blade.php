@if ($paginator->hasPages())
    <ul class="pagination" role="navigation" aria-label="Pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="pagination-previous disabled">Previous <span class="show-for-sr">page</span></li>
        @else
            <li class="pagination-previous"><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">Previous <span class="show-for-sr">page</span></a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="ellipsis" aria-hidden="true"></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="current"><span class="show-for-sr">You're on page </span>{{ $page }}</li>
                    @else
                        <li><a href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="pagination-next"><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">Next <span class="show-for-sr">page</span></a></li>
        @else
            <li class="pagination-next disabled">Next <span class="show-for-sr">page</span></li>
        @endif
    </ul>
@endif
