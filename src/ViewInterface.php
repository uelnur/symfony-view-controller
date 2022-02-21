<?php

namespace Uelnur\SymfonyViewController;

use Throwable;

interface ViewInterface {
    public function createViewContext(): BaseViewContext;

    // Before Controller action call
    public function init(BaseViewContext $viewContext): void;

    // After Controller action call, after Middleware afterAction call
    public function handle(BaseViewContext $viewContext): void;
    public function postHandle(BaseViewContext $viewContext): void;

    public function finish(BaseViewContext $viewContext): void;
    public function onException(Throwable $exception, BaseViewContext $viewContext): void;

    public function getViewRoute(BaseViewContext $viewContext): ViewRouteInterface;

    public function getViewMiddlewares(): array;
}
