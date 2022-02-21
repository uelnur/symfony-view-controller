<?php

namespace App\Integration\View\ViewMiddleware\Search;

use Uelnur\SymfonyViewController\ViewContext;
use Uelnur\SymfonyViewController\ViewMiddlewareInterface;
use App\Integration\View\ViewCommon\DoctrineList\DoctrineListBuildCriteriaBehavior;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;

class SearchViewMiddleware implements ViewMiddlewareInterface, DoctrineListBuildCriteriaBehavior {
    public function supportsViewContextTrait(): ?string {
        return SearchViewMiddlewareTrait::class;
    }

    public function supportsViewTrait(): ?string {
        return null;
    }

    public function supports(ViewContext $viewContext): bool {
        return true;
    }

    public function init(ViewContext $viewContext): void {
        /** @var $viewContext SearchViewMiddlewareTrait&ViewContext */

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

    public function afterAction(ViewContext $viewContext): void {

    }

    public function postHandle(ViewContext $viewContext): void {

    }

    public function finish(ViewContext $viewContext): void {

    }

    public function doctrineListBuildCriteria(AbstractCriteria $criteria, ViewContext $viewContext): void {
        /** @var $viewContext SearchViewMiddlewareTrait */

        if ( !$viewContext->search ) {
            return;
        }

        $criteria->withSearchText($viewContext->search);
    }
}
