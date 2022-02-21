<?php

namespace App\Integration\View\ViewCommon\DoctrineList;

use Uelnur\SymfonyViewController\ViewContext;
use Uelnur\SymfonyCriteriaRepository\AbstractCriteria;

interface DoctrineListBuildCriteriaBehavior {
    public function doctrineListBuildCriteria(AbstractCriteria $criteria, ViewContext $viewContext): void;
}
