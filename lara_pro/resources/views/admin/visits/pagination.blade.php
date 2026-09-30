@if ($paginator->hasPages())
    <nav class="va-pagination" aria-label="{{ $label }} pagination">
        <span>{{ number_format($paginator->firstItem()) }}–{{ number_format($paginator->lastItem()) }} of {{ number_format($paginator->total()) }}</span>
        <div>
            @if ($paginator->onFirstPage())<span class="ghost-button" aria-disabled="true">Previous</span>@else<a class="ghost-button" href="{{ $paginator->previousPageUrl() }}#{{ $anchor }}">Previous</a>@endif
            <span>Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            @if ($paginator->hasMorePages())<a class="ghost-button" href="{{ $paginator->nextPageUrl() }}#{{ $anchor }}">Next</a>@else<span class="ghost-button" aria-disabled="true">Next</span>@endif
        </div>
    </nav>
@endif
