<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Gateways\DatabaseGateway;
use Throwable;

class RunMigrations extends Usecase
{
    private DatabaseGateway $_databaseGateway;

    public function __construct(DatabaseGateway $databaseGateway)
    {
        $this->_databaseGateway = $databaseGateway;
    }

    public function perform(mixed $params = null): Result
    {
        try {
            $this->_databaseGateway->runMigrations();
            return new Result(
                response: new ApiResponse(
                    success: true,
                    code: 201,
                    message: "Migrations has run succesfully.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Migrations.",
                    trace: $params,
                    file: __FILE__,
                ),
            );
        } catch (Throwable $err) {
            return new Result(
                response: new ApiResponse(
                    success: false,
                    code: 500,
                    message: "An internal error occured.",
                ),
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
