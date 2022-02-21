<?php

namespace Uelnur\SymfonyViewController;

interface ViewMiddlewareInterface {
    public function supportsViewContextTrait(): ?string;

    public function supportsViewTrait(): ?string;

    public function supports(ViewContext $viewContext): bool;

    // after View init call
    public function init(ViewContext $viewContext): void;

    // after Controller action call
    public function afterAction(ViewContext $viewContext): void;

    // after View handle call
    public function postHandle(ViewContext $viewContext): void;

    // after View finish call
    public function finish(ViewContext $viewContext): void;
}
