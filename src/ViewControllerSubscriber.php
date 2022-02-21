<?php

namespace Uelnur\SymfonyViewController;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewControllerSubscriber implements EventSubscriberInterface {
    /** @var $viewMiddlewaresRegistry \Uelnur\SymfonyViewController\ViewMiddlewareInterface[] */
    private array $viewMiddlewaresRegistry = [];

    public function __construct(
        #[TaggedIterator(tag: 'uelnur.view.middleware')]
        iterable $middlewares,
        private ViewRouteManager $viewRouteManager,
    ) {
        foreach ($middlewares as $middleware) {
            $this->viewMiddlewaresRegistry[get_class($middleware)] = $middleware;
        }
    }

    public function onKernelController(ControllerEvent $event): void {
        if ( !$event->isMainRequest() ) {
            return;
        }

        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ( $controller instanceof ViewInterface ) {
            $view = $controller;
            $viewContext = $controller->createViewContext();

            $viewContext->view = $view;
            $viewContext->request = $event->getRequest();
            $viewContext->route = $event->getRequest()->attributes->get('_route');
            $viewContext->routeParams = $event->getRequest()->attributes->get('_route_params');

            $view->init($viewContext);

            if ( $viewContext->response ) {
                $this->setController($event, $viewContext->response);
                return;
            }

            $this->prepareMiddlewares($view, $viewContext);

            $this->handleViewMiddlewares($viewContext, 'init');

            if ( $viewContext->response ) {
                $this->setController($event, $viewContext->response);
                return;
            }

            $event->getRequest()->attributes->set('viewContext', $viewContext);
        }
    }

    public function onKernelView(ViewEvent $event) {
        if ( !$event->isMainRequest() ) {
            return;
        }

        $request = $event->getRequest();
        $viewContext = $request->attributes->get('viewContext');

        if ( $viewContext instanceof BaseViewContext ) {
            $viewContext->viewResult = $event->getControllerResult();
            $view = $viewContext->view;

            // Checking Firewall Access
            if ( $view instanceof ViewFirewallInterface && $view->viewIsGranted($viewContext) === false ) {
                $view->viewIfNotGranted($viewContext);

                if ( $viewContext->response ) {
                    $event->setResponse($viewContext->response);
                    return;
                }
            }

            $this->viewRouteManager->setCurrentViewRoute($view->getViewRoute($viewContext));

            $this->handleViewMiddlewares($viewContext, 'afterAction');

            if ( $viewContext->response ) {
                $event->setResponse($viewContext->response);
                return;
            }

            $view->handle($viewContext);

            if ( $viewContext->response ) {
                $event->setResponse($viewContext->response);
                return;
            }

            $this->handleViewMiddlewares($viewContext, 'postHandle');

            if ( $viewContext->response ) {
                $event->setResponse($viewContext->response);
                return;
            }

            $view->postHandle($viewContext);

            if ( $viewContext->response ) {
                $event->setResponse($viewContext->response);
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event) {
        if ( !$event->isMainRequest() ) {
            return;
        }

        $request = $event->getRequest();
        $viewContext = $request->attributes->get('viewContext');

        if ( $viewContext instanceof BaseViewContext ) {
            $viewContext->response = $event->getResponse();

            $view = $viewContext->view;
            $view->finish($viewContext);
            $this->handleViewMiddlewares($viewContext, 'finish');
        }
    }

    public function onKernelException(ExceptionEvent $event) {
        if ( !$event->isMainRequest() ) {
            return;
        }

        $request = $event->getRequest();
        $viewContext = $request->attributes->get('viewContext');

        if ( $viewContext instanceof BaseViewContext ) {
            $view = $viewContext->view;
            $exception = $event->getThrowable();
            $view->onException($exception, $viewContext);
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::VIEW => 'onKernelView',
            KernelEvents::RESPONSE => 'onKernelResponse',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    private function prepareMiddlewares(ViewInterface $view, BaseViewContext $viewContext) {
        $middlewareNames = $view->getViewMiddlewares();

        foreach ($middlewareNames as $middlewareName) {
            $middleware = $this->viewMiddlewaresRegistry[$middlewareName] ?? null;

            if (
                $middleware->supports($viewContext) &&
                (!$middleware->supportsViewContextTrait() || in_array($middleware->supportsViewContextTrait(), $this->classUsesDeep($viewContext))) &
                (!$middleware->supportsViewTrait() || in_array($middleware->supportsViewTrait(), $this->classUsesDeep($viewContext->view)))
            ) {
                $viewContext->viewMiddlewares[] = $middleware;
            }
        }
    }

    private function classUsesDeep($class, bool $autoload = true): array {
        $traits = [];
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while($class = get_parent_class($class));
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }
        return array_unique($traits);
    }

    private function handleViewMiddlewares(BaseViewContext $viewContext, string $method): void {
        foreach ($viewContext->viewMiddlewares as $middleware) {
            assert( $middleware instanceof ViewMiddlewareInterface );

            switch ($method) {
                case 'init':
                    $middleware->init($viewContext);
                    break;
                case 'afterAction':
                    $middleware->afterAction($viewContext);
                    break;
                case 'postHandle':
                    $middleware->postHandle($viewContext);
                    break;
                case 'finish':
                    $middleware->finish($viewContext);
                    break;
            }
        }
    }

    public function setController(ControllerEvent $event, Response $response) {
        $event->setController(function() use ($response) {
            return $response;
        });
    }
}