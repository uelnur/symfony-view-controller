<?php

namespace App\Integration\View\ViewMiddleware\Pagination;

class SlidingPagination extends AbstractPagination
{
    private string $route = '';
    private array $params;
    private int $pageRange = 5;
    private string $template = '';
    private string $sortableTemplate = '';
    private string $filtrationTemplate = '';

    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function setUsedRoute($route): void {
        $this->route = $route;
    }

    public function getRoute(): string {
        return $this->route;
    }

    public function setSortableTemplate($template): void {
        $this->sortableTemplate = $template;
    }

    public function getSortableTemplate(): string {
        return $this->sortableTemplate;
    }

    public function setFiltrationTemplate($template): void {
        $this->filtrationTemplate = $template;
    }

    public function getFiltrationTemplate(): string {
        return $this->filtrationTemplate;
    }

    public function setParam($name, $value): void {
        $this->params[$name] = $value;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function setTemplate($template): void {
        $this->template = $template;
    }

    public function getTemplate(): string {
        return $this->template;
    }

    public function setPageRange($range): void {
        $this->pageRange = abs((int)$range);
    }

    /**
     * Get url query with all parameters
     *
     * @param array $additionalQueryParams
     * @return array - list of query parameters
     */
    public function getQuery(array $additionalQueryParams = array()): array {
        return array_merge($this->params, $additionalQueryParams);
    }

    public function isSorted($key, array $params = array()): bool {
        $params = array_merge($this->params, $params);

        return isset($params[$this->getPaginatorOption('sortFieldParameterName')]) && $params[$this->getPaginatorOption('sortFieldParameterName')] === $key;
    }

    public function getPage(): mixed
    {
        return $this->params[$this->getPaginatorOption('pageParameterName')] ?? null;
    }

    public function getSort(): mixed
    {
        return $this->params[$this->getPaginatorOption('sortFieldParameterName')] ?? null;
    }

    public function getDirection(): mixed
    {
        return $this->params[$this->getPaginatorOption('sortDirectionParameterName')] ?? null;
    }

    public function getPaginationData(): array {
        $pageCount = $this->getPageCount();
        $current = $this->currentPageNumber;

        if ($pageCount < $current)
        {
            $this->currentPageNumber = $current = $pageCount;
        }

        if ($this->pageRange > $pageCount)
        {
            $this->pageRange = $pageCount;
        }

        $delta = ceil($this->pageRange / 2);

        if ($current - $delta > $pageCount - $this->pageRange)
        {
            $pages = range($pageCount - $this->pageRange + 1, $pageCount);
        }
        else
        {
            if ($current - $delta < 0)
            {
                $delta = $current;
            }

            $offset = $current - $delta;
            $pages = range($offset + 1, $offset + $this->pageRange);
        }

        $proximity = floor($this->pageRange / 2);

        $startPage  = $current - $proximity;
        $endPage    = $current + $proximity;

        if ($startPage < 1)
        {
            $endPage = min($endPage + (1 - $startPage), $pageCount);
            $startPage = 1;
        }

        if ($endPage > $pageCount)
        {
            $startPage = max($startPage - ($endPage - $pageCount), 1);
            $endPage = $pageCount;
        }

        $viewData = array(
            'last'              => $pageCount,
            'current'           => $current,
            'numItemsPerPage'   => $this->numItemsPerPage,
            'first'             => 1,
            'pageCount'         => $pageCount,
            'totalCount'        => $this->totalCount,
            'pageRange'         => $this->pageRange,
            'startPage'         => $startPage,
            'endPage'           => $endPage
        );

        if ($current > 1)
        {
            $viewData['previous'] = $current - 1;
        }

        if ($current < $pageCount)
        {
            $viewData['next'] = $current + 1;
        }

        $viewData['pagesInRange'] = $pages;
        $viewData['firstPageInRange'] = min($pages);
        $viewData['lastPageInRange']  = max($pages);

        if ($this->getItems() !== null)
        {
            $viewData['currentItemCount'] = $this->count();
            $viewData['firstItemNumber'] = (($current - 1) * $this->numItemsPerPage) + 1;
            $viewData['lastItemNumber'] = $viewData['firstItemNumber'] + $viewData['currentItemCount'] - 1;
        }

        return $viewData;
    }

    public function render(array $queryParams = array(), array $viewParams = array()): array {
        $data = $this->getPaginationData();

        $data['route'] = $this->getRoute();
        $data['query'] = array_merge($this->getParams(), $queryParams);

        return array_merge(
            $this->getPaginatorOptions(), // options given to paginator when paginated
            $this->getCustomParameters(), // all custom parameters for view
            $viewParams, // additional custom parameters for view
            $data // merging base route parameters last, to avoid broke of integrity
        );
    }

    public function getPageCount(): int
    {
        if ( !$this->numItemsPerPage )
        {
            return 1;
        }

        return (int)ceil($this->totalCount / $this->numItemsPerPage);
    }

    public function getPaginatorOptions(): array
    {
        return $this->paginatorOptions;
    }

    public function getCustomParameters(): array
    {
        return $this->customParameters;
    }
}
