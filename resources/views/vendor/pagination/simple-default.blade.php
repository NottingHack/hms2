@if ($paginator->hasPages())
<ul class="pagination" role="navigation" aria-label="Pagination">
  {{-- Previous Page Link --}}
  @if ($paginator->onFirstPage())
  <li class="pagination-previous disabled">@lang('pagination.previous')</li>
  @else
  <li class="pagination-previous"><a href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a></li>
  @endif

  {{-- Next Page Link --}}
  @if ($paginator->hasMorePages())
  <li class="pagination-next"><a href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a></li>
  @else
  <li class="pagination-next disabled">@lang('pagination.next')</li>
  @endif
</ul>
@endif
