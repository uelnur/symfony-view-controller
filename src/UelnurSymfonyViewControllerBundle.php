<?php

namespace Uelnur\SymfonyViewController;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UelnurSymfonyViewControllerBundle extends Bundle {
    public function build(ContainerBuilder $container) {
        $definitions[] = new Definition(ViewControllerSubscriber::class);
        $definitions[] = new Definition(ViewRouteManager::class);

        $container->addDefinitions($definitions);
        $container->registerForAutoconfiguration(ViewMiddlewareInterface::class)->addTag('uelnur_view_controller.middleware');
        $container->registerForAutoconfiguration(ViewInterface::class)->addTag('uelnur_view_controller.view');
    }
}
