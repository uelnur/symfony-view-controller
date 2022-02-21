<?php

namespace Uelnur\SymfonyViewController;

class Route
{
    public string $route;
    public array $params = [];

    public function __construct(string $route, array $params = [])
    {
        $this->route = $route;
        $this->params = $params;
    }
}
