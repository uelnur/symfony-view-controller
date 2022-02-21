<?php

namespace Uelnur\SymfonyViewController\Common\DoctrineList;

use Uelnur\SymfonyViewController\AbstractTwigView;
use Uelnur\SymfonyViewController\BaseViewContext;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;
use Uelnur\SymfonyCriteriaRepository\AbstractDoctrineRepository;
use Uelnur\SymfonyViewController\Middleware\Pagination\PaginationViewMiddleware;
use Uelnur\SymfonyViewController\Middleware\Search\SearchViewMiddleware;

abstract class DoctrineListView extends AbstractTwigView {
    abstract public function getRepository(): AbstractDoctrineRepository;

    public function createViewContext(): BaseViewContext {
        return new DoctrineListContext();
    }

    public function handle(BaseViewContext $viewContext): void {
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

    public function buildCriteriaBehavior(AbstractCriteria $criteria, BaseViewContext $viewContext): void {
        $this->buildCriteria($criteria, $viewContext);

        $this->runBehavior(
            DoctrineListBuildCriteriaBehavior::class,
            $viewContext,
            function(DoctrineListBuildCriteriaBehavior $middleware) use ($criteria, $viewContext) {
                $middleware->doctrineListBuildCriteria($criteria, $viewContext);
            },
        );
    }

    public function buildCriteria(AbstractCriteria $criteria, BaseViewContext $viewContext): void {}

    public function getViewMiddlewares(): array {
        return array_merge(parent::getViewMiddlewares(), [
            SearchViewMiddleware::class,
            PaginationViewMiddleware::class,
        ]);
    }
}
