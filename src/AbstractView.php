<?php

namespace Uelnur\SymfonyViewController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;

abstract class AbstractView extends AbstractController implements ViewInterface, ViewFirewallInterface {
    public function createViewContext(): ViewContext {
        return new ViewContext();
    }

    public function init(ViewContext $viewContext): void {}
    public function handle(ViewContext $viewContext): void {}

    public function postHandle(ViewContext $viewContext): void {}

    public function finish(ViewContext $viewContext): void {}

    public function onException(Throwable $exception, ViewContext $viewContext): void {}

    public function viewIfNotGranted(ViewContext $viewContext): void {
        throw $this->createAccessDeniedException();
    }

    /**
     * @return string[]
     */
    public function getViewMiddlewares(): array {
        return [];
    }

    public function runBehavior(string $behaviorInterface, ViewContext $viewContext, callable $callback): void {
        foreach ($viewContext->viewMiddlewares as $middleware) {
            if ( $middleware instanceof $behaviorInterface ) {
                $callback($middleware);
            }
        }
    }
}
