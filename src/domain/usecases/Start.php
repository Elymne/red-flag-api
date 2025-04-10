<?php

namespace Domain\Usecases;

use Domain\Gateways\DatabaseGateway;
use Domain\Gateways\EnvGateway;
use Domain\Gateways\RouterGateway;

class Start
{
    private DatabaseGateway $_databaseGateway;
    private RouterGateway $_routerGateway;

    public function __construct(DatabaseGateway $databaseGateway, RouterGateway $routerGateway)
    {
        $this->_databaseGateway = $databaseGateway;
        $this->_routerGateway = $routerGateway;
    }

    public function perform(): void
    {
        if ($_ENV["MODE"] == "develop") {
            ini_set('display_errors', 1);
        }

        if ($_ENV["MODE"] == "prod") {
            ini_set('display_errors', 0);
        }

        $this->_databaseGateway->checkDatabase();

        $this->_routerGateway->start();
    }
}
