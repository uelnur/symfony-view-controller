<?php

namespace Uelnur\SymfonyViewController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseViewContext {
    public ViewInterface $view;
    public mixed $viewResult = null;
    public array $viewMiddlewares = [];

    public Request $request;
    public ?Response $response = null;

    public ?string $route = '';
    public ?array $routeParams = [];
}
