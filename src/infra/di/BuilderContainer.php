<?php

namespace Infra\Di;

use Domain\Gateways\DatabaseGateway;
use Domain\Gateways\RouterGateway;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\LocalZoneRepository;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Usecases\FindCities;
use Domain\Usecases\InsertPerson;
use Domain\Usecases\Run;
use Domain\Usecases\RunMigrations;
use Infra\Datasources\DBConnect;
use Infra\Datasources\GeoApiDatasource;
use Infra\Datasources\PersonMysqlDatasource;
use Infra\Datasources\ZoneMysqlDatasource;
use Infra\Router\Router;

class BuilderContainer
{
    public static function injectAll()
    {
        self::_injectGateways();
        self::_injectRepositories();
        self::_injectUsecases();
    }

    private static function _injectGateways(): void
    {
        $container = Container::get();

        $container->add(DatabaseGateway::class, function () {
            return DBConnect::get();
        });

        $container->add(RouterGateway::class, function () {
            return new Router();
        });
    }

    /**
     * Inject all objects that implements my repositories.
     */
    private static function _injectRepositories(): void
    {
        $container = Container::get();

        $container->add(RemoteZoneRepository::class, function () {
            return new GeoApiDatasource();
        });

        $container->add(LocalPersonRepository::class, function () use ($container) {
            return new PersonMysqlDatasource($container->resolve(DatabaseGateway::class));
        });

        $container->add(LocalZoneRepository::class, function () use ($container) {
            return new ZoneMysqlDatasource($container->resolve(DatabaseGateway::class));
        });
    }

    private static function _injectUsecases(): void
    {
        $container = Container::get();

        $container->add(Run::class, function () use ($container) {
            return new Run(
                $container->resolve(RouterGateway::class),
            );
        });

        $container->add(RunMigrations::class, function () use ($container) {
            return new RunMigrations(
                $container->resolve(DatabaseGateway::class),
            );
        });

        $container->add(FindCities::class, function () use ($container) {
            return new FindCities();
        });

        $container->add(InsertPerson::class, function () use ($container) {
            return new InsertPerson(
                $container->resolve(DatabaseGateway::class),
                $container->resolve(LocalPersonRepository::class),
                $container->resolve(LocalZoneRepository::class),
            );
        });
    }
}
