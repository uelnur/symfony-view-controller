<?php

namespace Uelnur\SymfonyViewController;

interface ViewRouteInterface {
    public function getRoute(): ViewRoute;
    public function getParent(): ?ViewRouteInterface;
    public function getTitle(): ?string;
}
