<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Gateways\EnvGateway;
use Throwable;

class LoadEnv extends Usecase
{
    private EnvGateway $_envGateway;

    public function __construct(EnvGateway $_envGateway)
    {
        $this->_envGateway = $_envGateway;
    }

    public function perform(mixed $params = null): Result
    {
        try {
            $this->_envGateway->load();
            return new Result(
                code: 0,
                logData: new LogData(
                    type: LogData::INTERNAL,
                    message: "Action success : Env loaded",
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
