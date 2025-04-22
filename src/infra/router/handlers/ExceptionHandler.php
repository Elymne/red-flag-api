<?php

declare(strict_types=1);

namespace Infra\Router;

use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\SimpleRouter\Handlers\IExceptionHandler;

class ExceptionHandler implements IExceptionHandler
{
    public function handleError(Request $request, \Exception $error): void
    {
        // * Catch all non matching routes for me.
        if ($error instanceof NotFoundHttpException) {
            http_response_code(404);
            echo "This route does not exists.";
            exit;
        }

        // * Should never happen because all my usecase are use try/catch.
        http_response_code(500);
        echo "An error that was not catched by Server has been thrown.";
        exit;
    }
}
