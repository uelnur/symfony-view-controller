<?php

namespace Uelnur\SymfonyViewController;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UelnurSymfonyViewControllerBundle extends Bundle {
    public function build(ContainerBuilder $container) {
        parent::build($container);

        $definitions[] = (new Definition(ViewControllerSubscriber::class))
            ->setAutoconfigured(true)
            ->setAutowired(
            true);

        $definitions[] = (new Definition(ViewRouteManager::class))
            ->setAutoconfigured(true)
            ->setAutowired(true);

        $container->addDefinitions($definitions);
        $container->registerForAutoconfiguration(ViewMiddlewareInterface::class)->addTag('uelnur_view_controller.middleware');
        $container->registerForAutoconfiguration(ViewInterface::class)->addTag('uelnur_view_controller.view');
    }
}
