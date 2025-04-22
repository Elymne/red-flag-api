<?php

declare(strict_types=1);

namespace Infra\Di;

use Domain\Gateways\DatabaseGateway;
use Domain\Gateways\RouterGateway;
use Domain\Repositories\LocalDomainRepository;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\LocalZoneRepository;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Repositories\UuidRepository;
use Domain\Usecases\FindLocalZones;
use Domain\Usecases\FindPersons;
use Domain\Usecases\FindRemoteZones;
use Domain\Usecases\FindUniquePerson;
use Domain\Usecases\InsertLink;
use Domain\Usecases\InsertMessage;
use Domain\Usecases\InsertPerson;
use Domain\Usecases\Run;
use Domain\Usecases\RunMigrations;
use Infra\Uuid\RamseyUuid;
use Infra\Router\Router;
use Infra\Datasources\DBConnect;
use Infra\Datasources\DomainMysqlDatasource;
use Infra\Datasources\GeoApiDatasource;
use Infra\Datasources\PersonMysqlDatasource;
use Infra\Datasources\ZoneMysqlDatasource;

class BuilderContainer
{
    public static function injectAll()
    {
        $container = Container::get();

        self::_injectGateways($container);
        self::_injectRepositories($container);
        self::_injectUsecases($container);
    }

    /**
     * Inject my core libraries.
     */
    private static function _injectGateways(Container $container): void
    {
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
    private static function _injectRepositories(Container $container): void
    {
        $container->add(UuidRepository::class, function () {
            return new RamseyUuid();
        });

        $container->add(RemoteZoneRepository::class, function () {
            return new GeoApiDatasource();
        });

        $container->add(LocalPersonRepository::class, function () use ($container) {
            return new PersonMysqlDatasource($container->resolve(DatabaseGateway::class));
        });

        $container->add(LocalZoneRepository::class, function () use ($container) {
            return new ZoneMysqlDatasource($container->resolve(DatabaseGateway::class));
        });

        $container->add(LocalDomainRepository::class, function () use ($container) {
            return new DomainMysqlDatasource($container->resolve(DatabaseGateway::class));
        });
    }

    /**
     * Injects all my usecases.
     */
    private static function _injectUsecases(Container $container): void
    {
        $container->add(Run::class, function () use ($container): Run {
            return new Run($container->resolve(RouterGateway::class));
        });

        $container->add(RunMigrations::class, function () use ($container): RunMigrations {
            return new RunMigrations($container->resolve(DatabaseGateway::class));
        });

        $container->add(FindLocalZones::class, function () use ($container): FindLocalZones {
            return new FindLocalZones($container->resolve(LocalZoneRepository::class));
        });

        $container->add(FindRemoteZones::class, function () use ($container): FindRemoteZones {
            return new FindRemoteZones($container->resolve(RemoteZoneRepository::class));
        });

        $container->add(FindPersons::class, function () use ($container): FindPersons {
            return new FindPersons($container->resolve(LocalPersonRepository::class));
        });

        $container->add(FindUniquePerson::class, function () use ($container): FindUniquePerson {
            return new FindUniquePerson($container->resolve(LocalPersonRepository::class));
        });

        $container->add(InsertLink::class, function () use ($container): InsertLink {
            return new InsertLink(
                $container->resolve(UuidRepository::class),
                $container->resolve(LocalPersonRepository::class),
                $container->resolve(LocalDomainRepository::class),
            );
        });

        $container->add(InsertMessage::class, function () use ($container): InsertMessage {
            return new InsertMessage(
                $container->resolve(UuidRepository::class),
                $container->resolve(LocalPersonRepository::class)
            );
        });

        $container->add(InsertPerson::class, function () use ($container): InsertPerson {
            return new InsertPerson(
                $container->resolve(UuidRepository::class),
                $container->resolve(DatabaseGateway::class),
                $container->resolve(RemoteZoneRepository::class),
                $container->resolve(LocalPersonRepository::class),
                $container->resolve(LocalZoneRepository::class),
            );
        });
    }
}
