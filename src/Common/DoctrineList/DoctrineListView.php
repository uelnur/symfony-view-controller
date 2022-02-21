<?php

namespace App\Integration\View\ViewCommon\DoctrineList;

use Uelnur\SymfonyViewController\AbstractTwigView;
use Uelnur\SymfonyViewController\ViewContext;
use App\Integration\View\ViewMiddleware\Pagination\PaginationViewMiddleware;
use App\Integration\View\ViewMiddleware\Search\SearchViewMiddleware;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;
use Uelnur\SymfonyCriteriaRepository\AbstractDoctrineRepository;

abstract class DoctrineListView extends AbstractTwigView {
    abstract public function getRepository(): AbstractDoctrineRepository;

    public function createViewContext(): ViewContext {
        return new DoctrineListContext();
    }

    public function handle(ViewContext $viewContext): void {
        assert($viewContext instanceof DoctrineListContext);
        $repository = $this->getRepository();

        $criteria = $repository->createCriteria();
        $this->buildCriteriaBehavior($criteria, $viewContext);

        $entities = $repository->getResult($criteria);

        if ( !$criteria->limit || count($entities) === 0 ) {
            $viewContext->totalCount = count($entities);
        } else {
            $newCriteria = clone $criteria;
            $newCriteria->clearOrderBy();
            $newCriteria->limit = 0;

            $viewContext->totalCount = $this->getRepository()->getCount($newCriteria);
        }

        $viewContext->entities = $entities;
    }

    public function buildCriteriaBehavior(AbstractCriteria $criteria, ViewContext $viewContext): void {
        $this->buildCriteria($criteria, $viewContext);

        $this->runBehavior(
            DoctrineListBuildCriteriaBehavior::class,
            $viewContext,
            function(DoctrineListBuildCriteriaBehavior $middleware) use ($criteria, $viewContext) {
                $middleware->doctrineListBuildCriteria($criteria, $viewContext);
            },
        );
    }

    public function buildCriteria(AbstractCriteria $criteria, ViewContext $viewContext): void {}

    public function getViewMiddlewares(): array {
        return array_merge(parent::getViewMiddlewares(), [
            SearchViewMiddleware::class,
            PaginationViewMiddleware::class,
        ]);
    }
}
