<?php

namespace Uelnur\SymfonyViewController\Middleware\Pagination;

use Uelnur\SymfonyViewController\BaseViewContext;
use Uelnur\SymfonyViewController\Common\DoctrineList\DoctrineListBuildCriteriaBehavior;
use Uelnur\SymfonyViewController\Common\DoctrineList\DoctrineListContext;
use Uelnur\SymfonyViewController\ViewMiddlewareInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;

class PaginationViewMiddleware implements ViewMiddlewareInterface, DoctrineListBuildCriteriaBehavior {
    private PaginationFactory $paginationFactory;
    private SessionInterface $session;

    public function __construct(PaginationFactory $paginationFactory, SessionInterface $session)
    {
        $this->paginationFactory = $paginationFactory;
        $this->session = $session;
    }

    public function supportsViewContextTrait(): ?string {
        return PaginationViewMiddlewareTrait::class;
    }

    public function supportsViewTrait(): ?string {
        return null;
    }

    public function supports(BaseViewContext $viewContext): bool {
        return true;
    }

    public function init(BaseViewContext $viewContext): void {
        /** @var $viewContext PaginationViewMiddlewareTrait */

        if ( !$viewContext->paginationEnabled ) {
            return;
        }

        $maxPerPage = $viewContext->paginationMaxPerPage;
        $defaultPerPage = $viewContext->paginationDefaultPerPage;

        $defaultPerPage = (int)$this->session->get('pagination_' . get_class($viewContext->view) . '_per_page', $defaultPerPage);

        $request = $viewContext->request;
        $page = $request->query->getInt($viewContext->paginationPageField, $viewContext->paginationPage);
        $perPage = $request->query->getInt($viewContext->paginationPerPageField, $defaultPerPage);

        if ( $perPage < 1) {
            $perPage = 0;
        }

        if ( $page < 1 ) {
            $page = 1;
        }

        if ( $maxPerPage && $perPage > $maxPerPage ) {
            $perPage = $maxPerPage;
        }

        if ( $perPage ) {
            $viewContext->paginationPage = $page;
            $viewContext->paginationPerPage = $perPage;

            if ( $page !== 1 ) {
                $viewContext->routeParams[$viewContext->paginationPageField] = $page;
            }

            if ( $perPage !== $defaultPerPage ) {
                $viewContext->routeParams[$viewContext->paginationPerPageField] = $perPage;
            }
        }
    }

    public function afterAction(BaseViewContext $viewContext): void {

    }

    public function postHandle(BaseViewContext $viewContext): void {
        /** @var $viewContext PaginationViewMiddlewareTrait */

        if ( !$viewContext->paginationEnabled ) {
            return;
        }

        if ( !$viewContext->paginationPerPage ) {
            return;
        }

        $this->session->set('pagination_' . get_class($viewContext->view) . '_per_page',  $viewContext->paginationPerPage);

        $totalCount = 0;

        if ( $viewContext instanceof DoctrineListContext /*|| $viewContext instanceof ElasticDoctrineListContext*/) {
            $totalCount = $viewContext->totalCount;
        }

        if ( !$totalCount ) {
            return;
        }

        $viewContext->paginationTotalCount = $totalCount;
        $viewContext->paginationConfig = $this->paginationFactory->create(
            $totalCount,
            $viewContext->paginationPage,
            $viewContext->paginationPerPage,
            null,
            $viewContext->routeParams,
        )->render();
    }

    public function finish(BaseViewContext $viewContext): void {

    }

    public function doctrineListBuildCriteria(AbstractCriteria $criteria, BaseViewContext $viewContext): void {
        /** @var $viewContext PaginationViewMiddlewareTrait */

        if ( !$viewContext->paginationEnabled ) {
            return;
        }

        if ( !$viewContext->paginationPerPage ) {
            return;
        }

        $criteria->withPagination($viewContext->paginationPage, $viewContext->paginationPerPage);
    }
}
