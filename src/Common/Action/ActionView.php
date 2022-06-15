<?php

namespace Uelnur\SymfonyViewController\Common\Action;

use Uelnur\SymfonyEntityAction\DispatchableActionDecorator;
use Uelnur\SymfonyEntityAction\LoggableActionDecorator;
use Uelnur\SymfonyEntityAction\ValidatableActionDecorator;
use Uelnur\SymfonyViewController\AbstractTwigView;
use Uelnur\SymfonyViewController\BaseViewContext;
use Exception;
use Uelnur\SymfonyEntityAction\ActionStatusInterface;

abstract class ActionView extends AbstractTwigView {
    public function createViewContext(): ActionViewContext {
        return new ActionViewContext();
    }

    /**
     * @throws \Exception
     */
    public function handle(BaseViewContext $viewContext): void {
        assert($viewContext instanceof ActionViewContext);

        $action = $viewContext->action;
        $action = new LoggableActionDecorator($action);
        $action = new ValidatableActionDecorator($action);
        $action = new DispatchableActionDecorator($action);
        $viewContext->action = $action;

        $viewContext->actionParams = $action->createParams();
        $this->onActionCreateParams($viewContext->actionParams, $viewContext);

        if ( $viewContext->response ) {
            return;
        }

        try {
            $viewContext->actionData = $action->createData($viewContext->actionParams);
        }
        catch (Exception $e) {
            $this->onActionCreateDataException($e, $viewContext->actionParams, $viewContext);
            return;
        }

        $this->onActionCreateData($viewContext->actionData, $viewContext);

        if ( $viewContext->response ) {
            return;
        }

        $viewContext->actionFormOptions = $this->getActionFormOptions($viewContext->actionData, $viewContext);

        if ( $viewContext->response ) {
            return;
        }

        $viewContext->actionForm = $this->createForm($viewContext->actionFormBuilderClass, $viewContext->actionData, $viewContext->actionFormOptions);

        $viewContext->actionForm->handleRequest($viewContext->request);
        $viewContext->actionFormView = $viewContext->actionForm->createView();

        if ( $this->actionShouldAutoSubmit($viewContext->actionData, $viewContext) || ($viewContext->actionForm->isSubmitted() && $viewContext->actionForm->isValid()) ) {
            try {
                $status = $action->handle($viewContext->actionData);
            }
            catch (Exception $e) {
                $this->onActionHandleException($e, $viewContext->actionData, $viewContext);
                return;
            }

            if ( $status->isSuccess() ) {
                $this->onActionHandleSuccess($status, $viewContext->actionData, $viewContext);
            }
            else {
                $this->onActionHandleFail($status, $viewContext->actionData, $viewContext);
            }
        }
    }

    public function actionShouldAutoSubmit(null|array|object $data, ActionViewContext $viewContext): bool {
        return false;
    }

    public function onActionCreateParams(null|array|object $params, ActionViewContext $viewContext): void {

    }

    public function onActionCreateData(null|array|object $data, ActionViewContext $viewContext): void {

    }

    /**
     * @throws \Exception
     */
    public function onActionCreateDataException(Exception $exception, null|array|object $params, ActionViewContext $viewContext): void {
        throw $exception;
    }

    public function onActionHandleSuccess(ActionStatusInterface $status, null|array|object $data, ActionViewContext $viewContext): void {

    }

    public function onActionHandleFail(ActionStatusInterface $status, null|array|object $data, ActionViewContext $viewContext): void {

    }

    /**
     * @throws \Exception
     */
    public function onActionHandleException(Exception $exception, null|array|object $data, ActionViewContext $viewContext): void {
        throw $exception;
    }

    public function getActionFormOptions(null|array|object $data, ActionViewContext $viewContext): array {
        return [];
    }
}
