<?php

namespace Uelnur\SymfonyViewController;

class AbstractTwigViewContext extends BaseViewContext {

    public ?string $template = '';
    public ?array $templateParams = [];
}
