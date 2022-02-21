<?php

namespace Uelnur\SymfonyViewController\Common\DoctrineList;

use Uelnur\SymfonyViewController\AbstractTwigViewContext;
use Uelnur\SymfonyViewController\Middleware\Pagination\PaginationViewMiddlewareTrait;
use Uelnur\SymfonyViewController\Middleware\Search\SearchViewMiddlewareTrait;

class DoctrineListContext extends AbstractTwigViewContext {
    use SearchViewMiddlewareTrait;
    use PaginationViewMiddlewareTrait;

    public array $entities = [];
    public int $totalCount = 0;
}
