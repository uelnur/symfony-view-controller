<?php

namespace Uelnur\SymfonyViewController;

interface ViewMiddlewareInterface {
    public function supportsViewContextTrait(): ?string;

    public function supportsViewTrait(): ?string;

    public function supports(BaseViewContext $viewContext): bool;

    // after View init call
    public function init(BaseViewContext $viewContext): void;

    // after Controller action call
    public function afterAction(BaseViewContext $viewContext): void;

    // after View handle call
    public function postHandle(BaseViewContext $viewContext): void;

    // after View finish call
    public function finish(BaseViewContext $viewContext): void;
}
