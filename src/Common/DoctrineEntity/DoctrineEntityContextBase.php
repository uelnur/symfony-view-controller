<?php

namespace Uelnur\SymfonyViewController\Common\DoctrineEntity;

use Uelnur\SymfonyViewController\BaseViewContext;

class DoctrineEntityContextBase extends BaseViewContext {
    public int|string|null $id = null;
    public array|object|null $entity = null;
}
