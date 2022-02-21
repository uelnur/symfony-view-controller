<?php

namespace Uelnur\SymfonyViewController;

interface ViewRouteInterface {
    public function getRoute(): RouteData;
    public function getParent(): ?ViewRouteInterface;
    public function getTitle(): ?string;
}
