<?php

namespace App\Integration\View\ViewCommon\DoctrineEntity;

use Uelnur\SymfonyViewController\AbstractTwigView;
use Uelnur\SymfonyViewController\ViewContext;

abstract class DoctrineEntityView extends AbstractTwigView {
    abstract public function getEntity(int|string $id): array|object;

    public function createViewContext(): ViewContext {
        return new DoctrineEntityContext();
    }

    public function handle(ViewContext $viewContext): void {
        assert($viewContext instanceof DoctrineEntityContext);
        $viewContext->entity = $this->getEntity($viewContext->id);
    }
}
