@if ($paginator->hasPages())
<nav class="pagination" aria-label="Paginanavigatie">
    @if ($paginator->onFirstPage())<span>← Vorige</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Vorige</a>@endif
    <span>Pagina {{ $paginator->currentPage() }} van {{ $paginator->lastPage() }}</span>
    @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">Volgende →</a>@else<span>Volgende →</span>@endif
</nav>
@endif
