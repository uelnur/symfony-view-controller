<?php

namespace App\Integration\View\ViewCommon\DoctrineEntity;

use Uelnur\SymfonyViewController\ViewContext;

class DoctrineEntityContext extends ViewContext {
    public int|string|null $id = null;
    public array|object|null $entity = null;
}
