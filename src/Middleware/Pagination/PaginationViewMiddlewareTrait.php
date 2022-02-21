<?php

namespace App\Integration\View\ViewMiddleware\Pagination;

trait PaginationViewMiddlewareTrait {
    public bool $paginationEnabled = true;
    public int $paginationPage = 1;
    public int $paginationPerPage = 0;
    public int $paginationMaxPerPage = 0;
    public int $paginationDefaultPerPage = 0;

    public string $paginationPageField = 'page';
    public string $paginationPerPageField = 'per_page';

    public null|array $paginationConfig = null;
    public int $paginationTotalCount = 0;
}
