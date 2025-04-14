<?php

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Gateways\RouterGateway;
use Throwable;

class Run extends Usecase
{
    private RouterGateway $_routerGateway;

    public function __construct(RouterGateway $routerGateway)
    {
        $this->_routerGateway = $routerGateway;
    }

    public function perform(mixed $params = null): Result
    {
        try {
            if ($_ENV["MODE"] == "develop") {
                ini_set('display_errors', 1);
            }

            if ($_ENV["MODE"] == "prod") {
                ini_set('display_errors', 0);
            }

            $this->_routerGateway->start();
            return new Result(code: 0, data: null);
        } catch (Throwable $err) {
            return new Result(code: 1, data: $err);
        }
    }
}
