<?php

namespace App\Integration\View\ViewMiddleware\Pagination;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;
use Uelnur\SymfonyCriteriaRepository\AbstractDoctrineRepository;

class PaginationFactory
{
    private ?Request $request;

    public function __construct(RequestStack $stack)
    {
        $this->request = $stack->getMainRequest();
    }

    public function create(int $count, int $page = 1, int $perPage = 100, string $route = null, array $routeParams = null): SlidingPagination
    {
        $route = $route?: $this->request->attributes->get('_route');
        $routeParams = $routeParams?: $this->request->attributes->get('_route_params');

        $pagination = new SlidingPagination($routeParams);
        $pagination->setCurrentPageNumber($page);
        $pagination->setItemNumberPerPage($perPage);
        $pagination->setTotalItemCount($count);
        $pagination->setPaginatorOptions([
            'pageParameterName' => 'page',
        ]);
        $pagination->setCustomParameters([
        ]);
        $pagination->setUsedRoute($route);

        return $pagination;
    }

    /**
     * @param \Uelnur\SymfonyCriteriaRepository\AbstractDoctrineRepository $matcher
     * @param \Uelnur\SymfonyCriteriaRepository\AbstractCriteria $criteria
     * @param string|null $route
     * @param array $routeParams
     * @return SlidingPagination
     * @throws \Uelnur\SymfonyCriteriaRepository\Exception\InvalidCriteria
     */
    public function createFromCriteria(AbstractDoctrineRepository $matcher, AbstractCriteria $criteria, string $route = null, array $routeParams = []): SlidingPagination
    {
        $offset = $criteria->offset;
        $limit = $criteria->limit;
        $orderBy = $criteria->getOrderBy();

        $criteria->offset = 0;
        $criteria->limit = null;
        $criteria->clearOrderBy();

        $count = $matcher->getCount($criteria);

        $criteria->withMaxResult($limit);
        $criteria->withResultOffset($offset);

        foreach ($orderBy as $field => $asc) {
            $criteria->orderedBy($field, $asc);
        }
        $page = 1;

        if ( $offset && $limit ) {
            $page = $offset / $limit + 1;
        }

        return $this->create($count, $page, $limit, $route, $routeParams);
    }
}
