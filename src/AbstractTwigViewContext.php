<?php

namespace Uelnur\SymfonyViewController;

class AbstractTwigViewContext extends ViewContext {

    public ?string $template = '';
    public ?array $templateParams = [];
}
