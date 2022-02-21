<?php

namespace Uelnur\SymfonyViewController\Common\DoctrineEntity;

use Uelnur\SymfonyViewController\AbstractTwigView;
use Uelnur\SymfonyViewController\BaseViewContext;

abstract class DoctrineEntityView extends AbstractTwigView {
    abstract public function getEntity(int|string $id): array|object;

    public function createViewContext(): BaseViewContext {
        return new DoctrineEntityContextBase();
    }

    public function handle(BaseViewContext $viewContext): void {
        assert($viewContext instanceof DoctrineEntityContextBase);
        $viewContext->entity = $this->getEntity($viewContext->id);
    }
}
