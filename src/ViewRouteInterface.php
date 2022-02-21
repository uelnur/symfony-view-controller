<?php

namespace Uelnur\SymfonyViewController;

interface ViewRouteInterface {
    public function getRoute(): Route;
    public function getParent(): ?ViewRouteInterface;
    public function getTitle(): ?string;
}
