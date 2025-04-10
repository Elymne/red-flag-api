<?php

namespace Infra\Di;

use Domain\Gateways\EnvGateway;
use Domain\Gateways\RouterGateway;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemoteCityRepository;
use Domain\Usecases\Start;
use Infra\Data\DBConnect;
use Infra\Datasources\GeoApiDatasource;
use Infra\Datasources\PersonMysqlDatasource;
use Infra\Env\Env;
use Infra\Router\Router;

class BuilderContainer
{
    public static function injectAll()
    {
        self::_injectGateways();
        self::_injectRepositories();
        self::_injectUsecases();
    }

    /**
     * Inject all objects that implements my repositories.
     */
    private static function _injectRepositories(): void
    {
        $container = Container::get();

        $container->add(RemoteCityRepository::class, function () {
            return new GeoApiDatasource();
        });

        $container->add(DBConnect::class, function () {
            return DBConnect::get();
        });

        $container->add(LocalPersonRepository::class, function () use ($container) {
            return new PersonMysqlDatasource($container->resolve(DBConnect::class));
        });
    }

    private static function _injectGateways(): void
    {
        $container = Container::get();

        $container->add(EnvGateway::class, function () {
            return new Env();
        });

        $container->add(RouterGateway::class, function () {
            return new Router();
        });
    }

    private static function _injectUsecases(): void
    {
        $container = Container::get();

        $container->add(Start::class, function () use ($container) {
            return new Start(
                $container->resolve(EnvGateway::class),
                $container->resolve(RouterGateway::class),
            );
        });
    }
}
