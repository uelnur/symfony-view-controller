<?php

namespace Uelnur\SymfonyViewController;

class ViewRouteManager {
    /** @var $parents ViewRouteInterface[] */
    private array $parents = [];

    public function setCurrentViewRoute(ViewRouteInterface $viewRoute): void {
        //$this->current = $hierarchyItem;
        $this->parents = [];

        $current = $viewRoute;

        do {
            $this->parents[] = $current;
            $current = $current->getParent();
        }
        while ($current instanceof ViewRouteInterface);
    }

    public function getBreadcrumbs(): array {
        $parents = $this->parents;

        return array_reverse($parents);
    }

    public function isRouteActive(string $route, ?array $params = []): bool {
        foreach ($this->parents as $item) {
            if ( $item->getRoute()->route !== $route ) {
                continue;
            }

            $itemParams = $item->getRoute()->params;
            $paramsValid = true;

            foreach ($itemParams as $k => $v) {
                if ( !($params[$k] ?? null) ) {
                    $paramsValid = false;
                    break;
                }

                if ( $params[$k] !== $v ) {
                    $paramsValid = false;
                    break;
                }
            }

            if ( $paramsValid ) {
                return true;
            }
        }

        return false;
    }
}