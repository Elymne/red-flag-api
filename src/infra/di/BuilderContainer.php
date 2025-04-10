<?php

namespace Infra\DI;

use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemoteCityRepository;
use Infra\Data\DBConnect;
use Infra\Datasources\GeoApiDatasource;
use Infra\Datasources\PersonMysqlDatasource;

class BuilderContainer
{
    public static function injectAll()
    {
        self::injectRepositories();
        self::buildUsecases();
    }

    /**
     * Inject all objects that implements my repositories.
     */
    private static function injectRepositories(): void
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

    private static function buildUsecases(): void {}
}
