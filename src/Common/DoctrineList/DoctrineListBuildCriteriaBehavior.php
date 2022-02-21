<?php

namespace Uelnur\SymfonyViewController\Common\DoctrineList;

use Uelnur\SymfonyViewController\BaseViewContext;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;

interface DoctrineListBuildCriteriaBehavior {
    public function doctrineListBuildCriteria(AbstractCriteria $criteria, BaseViewContext $viewContext): void;
}
