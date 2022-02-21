<?php

namespace Uelnur\SymfonyViewController;

abstract class AbstractTwigView extends AbstractView {

    public function createViewContext(): ViewContext {
        return new AbstractTwigViewContext();
    }

    public function postHandle(ViewContext $viewContext): void {
        assert($viewContext instanceof AbstractTwigViewContext);

        if ( !$viewContext->response && $viewContext->template ) {
            $viewContext->response = $this->render(
                $viewContext->template,
                array_merge($viewContext->templateParams, [
                    'context' => $viewContext,
                ]),
            );
        }
    }

    public function getViewMiddlewares(): array {
        return [
        ];
    }
}
