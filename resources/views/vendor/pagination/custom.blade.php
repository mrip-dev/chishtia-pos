@if ($paginator->onFirstPage())
    <li class="page-item disabled" aria-disabled="true">
        <span class="page-link"><i class="fas fa-angle-left"></i></span>
    </li>
@else
    <li class="page-item">
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
            <i class="fas fa-angle-left"></i>
        </a>
    </li>
@endif
