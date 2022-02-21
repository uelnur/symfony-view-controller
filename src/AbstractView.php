<?php

namespace Uelnur\SymfonyViewController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;

abstract class AbstractView extends AbstractController implements ViewInterface, ViewFirewallInterface {
    public function createViewContext(): BaseViewContext {
        return new BaseViewContext();
    }

    public function init(BaseViewContext $viewContext): void {}
    public function handle(BaseViewContext $viewContext): void {}

    public function postHandle(BaseViewContext $viewContext): void {}

    public function finish(BaseViewContext $viewContext): void {}

    public function onException(Throwable $exception, BaseViewContext $viewContext): void {}

    public function viewIfNotGranted(BaseViewContext $viewContext): void {
        throw $this->createAccessDeniedException();
    }

    /**
     * @return string[]
     */
    public function getViewMiddlewares(): array {
        return [];
    }

    public function runBehavior(string $behaviorInterface, BaseViewContext $viewContext, callable $callback): void {
        foreach ($viewContext->viewMiddlewares as $middleware) {
            if ( $middleware instanceof $behaviorInterface ) {
                $callback($middleware);
            }
        }
    }
}
