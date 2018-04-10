<?php

namespace Rest\Error;

use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Error\ExceptionRenderer;
use Cake\Log\Log;
use Rest\Controller\ErrorController;

class RestExceptionRenderer extends ExceptionRenderer
{
//    protected function _getController()
//    {
//        return new ErrorController();
//    }

    /**
     * Renders the response for the exception.
     *
     * @return \Cake\Http\Response The response to be sent.
     */
    public function render($showOriginamMessage = false)
    {
        Log::info('Rest\Error\RestExceptionRenderer::render');

        $exception = $this->error;
        $code = $this->_code($exception);

        \Cake\Log\Log::debug($exception);

        $unwrapped = $this->_unwrap($exception);

        if ($exception instanceof \Rest\Routing\Exception\MissingTokenException ||
            $exception instanceof \Rest\Routing\Exception\InvalidTokenException ||
            $exception instanceof \Rest\Routing\Exception\InvalidTokenFormatException
        ) {
            $message = $exception->getMessage();
        } else {
            $message = $this->_message($exception, $code);
        }

        if ($exception instanceof CakeException) {
            $this->controller->response->header($exception->responseHeader());
        }

        $this->controller->response->statusCode($code);

        $viewVars = [
            'message' => $message,
            'code' => $code
        ];

        $isDebug = Configure::read('debug');

        if ($isDebug) {
            $viewVars['trace'] = Debugger::formatTrace($unwrapped->getTrace(), [
                    'format' => 'array',
                    'args' => false
            ]);
            $viewVars['file'] = $exception->getFile() ? : 'null';
            $viewVars['line'] = $exception->getLine() ? : 'null';
        }

        $this->controller->set($viewVars);

        if ($unwrapped instanceof CakeException && $isDebug) {
            $this->controller->set($unwrapped->getAttributes());
        }

        return $this->_prepareResponse();
    }

    protected function _prepareResponse()
    {
        $this->controller->viewBuilder()->className('Rest.Json');

        return $this->controller->render();
    }
}