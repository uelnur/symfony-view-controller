<?php

namespace Uelnur\SymfonyViewController\Middleware\Search;

use Uelnur\SymfonyViewController\BaseViewContext;
use Uelnur\SymfonyViewController\Common\DoctrineList\DoctrineListBuildCriteriaBehavior;
use Uelnur\SymfonyViewController\ViewMiddlewareInterface;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;

class SearchViewMiddleware implements ViewMiddlewareInterface, DoctrineListBuildCriteriaBehavior {
    public function supportsViewContextTrait(): ?string {
        return SearchViewMiddlewareTrait::class;
    }

    public function supportsViewTrait(): ?string {
        return null;
    }

    public function supports(BaseViewContext $viewContext): bool {
        return true;
    }

    public function init(BaseViewContext $viewContext): void {
        /** @var $viewContext SearchViewMiddlewareTrait&BaseViewContext */

        $request = $viewContext->request;
        $search = $request->query->get($viewContext->searchField, '');
        $search = $this->prepareSearch($search);

        if ( !$search ) {
            return;
        }

        $viewContext->search = $search;
        $viewContext->routeParams[$viewContext->searchField] = $search;
    }

    private function prepareSearch(string $search): string {
        return trim($search);
    }

    public function afterAction(BaseViewContext $viewContext): void {

    }

    public function postHandle(BaseViewContext $viewContext): void {

    }

    public function finish(BaseViewContext $viewContext): void {

    }

    public function doctrineListBuildCriteria(AbstractCriteria $criteria, BaseViewContext $viewContext): void {
        /** @var $viewContext SearchViewMiddlewareTrait */

        if ( !$viewContext->search ) {
            return;
        }

        $criteria->withSearchText($viewContext->search);
    }
}
