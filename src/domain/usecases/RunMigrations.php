<?php

declare(strict_types=1);

namespace Domain\Usecases;

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
            return new Result(code: 200, data: "Migrations has run succesfully.");
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
