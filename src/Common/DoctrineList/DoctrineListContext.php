<?php

namespace App\Integration\View\ViewCommon\DoctrineList;

use Uelnur\SymfonyViewController\AbstractTwigViewContext;
use App\Integration\View\ViewMiddleware\Pagination\PaginationViewMiddlewareTrait;
use App\Integration\View\ViewMiddleware\Search\SearchViewMiddlewareTrait;

class DoctrineListContext extends AbstractTwigViewContext {
    use SearchViewMiddlewareTrait;
    use PaginationViewMiddlewareTrait;

    public array $entities = [];
    public int $totalCount = 0;
}
