<?php

namespace Domain\Usecases;

use Domain\Gateways\EnvGateway;
use Domain\Gateways\RouterGateway;

class Start
{
    private EnvGateway $_envGateway;
    private RouterGateway $_routerGateway;

    public function __construct(EnvGateway $_envGateway, RouterGateway $_routerGateway)
    {
        $this->_envGateway = $_envGateway;
        $this->_routerGateway = $_routerGateway;
    }

    public function perform(): void
    {
        $this->_envGateway->load();

        if ($_ENV["MODE"] == "develop") {
            ini_set('display_errors', 1);
        }

        if ($_ENV["MODE"] == "prod") {
            ini_set('display_errors', 0);
        }

        $this->_routerGateway->start();
    }
}
