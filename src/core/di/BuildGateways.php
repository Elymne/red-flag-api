<?php

declare(strict_types=1);

namespace Core;

use Domain\Gateways\DatabaseGateway;
use Domain\Gateways\EnvGateway;
use Domain\Gateways\LoggerGateway;
use Domain\Gateways\RouterGateway;
use Infra\Datasources\DBConnect;
use Infra\Env\DotEnv;
use Infra\Logger\LoggerMono;
use Infra\Router\Router;

class BuildGateways
{
    public static function inject(Container $container)
    {
        $container->add(LoggerGateway::class, function () {
            return new LoggerMono();
        });

        $container->add(EnvGateway::class, function () {
            return new DotEnv();
        });

        $container->add(DatabaseGateway::class, function () {
            return DBConnect::get();
        });

        $container->add(RouterGateway::class, function () {
            return new Router();
        });
    }
}
