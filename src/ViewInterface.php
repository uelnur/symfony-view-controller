<?php

namespace Uelnur\SymfonyViewController;

use Throwable;

interface ViewInterface {
    public function createViewContext(): ViewContext;

    // Before Controller action call
    public function init(ViewContext $viewContext): void;

    // After Controller action call, after Middleware afterAction call
    public function handle(ViewContext $viewContext): void;
    public function postHandle(ViewContext $viewContext): void;

    public function finish(ViewContext $viewContext): void;
    public function onException(Throwable $exception, ViewContext $viewContext): void;

    public function getViewRoute(ViewContext $viewContext): ViewRouteInterface;

    public function getViewMiddlewares(): array;
}
