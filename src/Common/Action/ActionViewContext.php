<?php

namespace Uelnur\SymfonyViewController\Common\Action;

use Uelnur\SymfonyViewController\AbstractTwigViewContext;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Uelnur\SymfonyEntityAction\ActionInterface;

class ActionViewContext extends AbstractTwigViewContext {
    public ?ActionInterface  $action                 = null;
    public array|object|null $actionParams           = null;
    public array|object|null $actionData             = null;
    public ?string           $actionFormBuilderClass = null;
    public ?array            $actionFormOptions      = [];
    public ?FormInterface    $actionForm             = null;
    public ?FormView         $actionFormView         = null;
}
