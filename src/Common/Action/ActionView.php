<?php

namespace Uelnur\SymfonyViewController\Common\Action;

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

        $actionParams = $action->createParams();
        $viewContext->actionParams = $actionParams;
        $this->onActionCreateParams($actionParams, $viewContext);

        if ( $viewContext->response ) {
            return;
        }

        try {
            $actionData = $action->createData($actionParams);
        }
        catch (Exception $e) {
            $this->onActionCreateDataException($e, $actionParams, $viewContext);
            return;
        }

        $viewContext->actionData = $actionData;
        $this->onActionCreateData($actionData, $viewContext);

        if ( $viewContext->response ) {
            return;
        }

        $actionFormOptions = $this->getActionFormOptions($actionData, $viewContext);

        if ( $viewContext->response ) {
            return;
        }

        $actionForm = $this->createForm($viewContext->actionFormBuilderClass, $actionData, $actionFormOptions);
        $viewContext->actionForm = $actionForm;

        $viewContext->actionForm->handleRequest($viewContext->request);
        $viewContext->actionFormView = $actionForm->createView();

        if ( $this->actionShouldAutoSubmit($actionData, $viewContext) || ($actionForm->isSubmitted() && $actionForm->isValid()) ) {
            try {
                $status = $action->handle($actionData);
            }
            catch (Exception $e) {
                $this->onActionHandleException($e, $actionData, $viewContext);
                return;
            }

            if ( $status->isSuccess() ) {
                $this->onActionHandleSuccess($status, $actionData, $viewContext);
            }
            else {
                $this->onActionHandleFail($status, $actionData, $viewContext);
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
