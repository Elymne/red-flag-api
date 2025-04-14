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
        /* The router will throw the NotFoundHttpException on 404 */
        if ($error instanceof NotFoundHttpException) {
            http_response_code(404);
            echo "This route does not exists.";
            exit;
        }

        http_response_code(500);
        echo "An error that was not catched by Server has been thrown.";
        exit;
    }
}
