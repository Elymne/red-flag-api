<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\LogData;
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
                header("Access-Control-Allow-Origin: *");
                ini_set("display_errors", 1);
            }

            if ($_ENV["MODE"] == "prod") {
                ini_set("display_errors", 0);
            }

            $this->_routerGateway->start();
            return new Result(
                code: 0,
                logData: new LogData(
                    type: LogData::INTERNAL,
                    message: "Action success : Route loaded",
                    file: __FILE__,
                ),
            );
        } catch (Throwable $err) {
            return new Result(
                code: 1,
                logData: new LogData(
                    type: LogData::CRITICAL,
                    message: "Action failure : Unexpected error occured.",
                    trace: $err,
                    file: __FILE__,
                ),
            );
        }
    }
}
