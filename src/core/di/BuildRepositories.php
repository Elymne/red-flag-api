<?php

declare(strict_types=1);

namespace Core;

use Domain\Gateways\DatabaseGateway;
use Domain\Repositories\LocalDomainRepository;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemoteActivityRepository;
use Domain\Repositories\RemoteCompanyRepository;
use Domain\Repositories\RemotePersonRepository;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Repositories\UuidRepository;
use Infra\Datasources\DataGouvCompanyDatasource;
use Infra\Datasources\DomainMysqlDatasource;
use Infra\Datasources\FranceTravailActivityDatasource;
use Infra\Datasources\GeoApiDatasource;
use Infra\Datasources\PersonMysqlDatasource;
use Infra\Datasources\WikiApiDatasource;
use Infra\Uuid\RamseyUuid;

class BuildRepositories
{
    public static function inject(Container $container)
    {
        $container->add(UuidRepository::class, function () {
            return new RamseyUuid();
        });

        $container->add(RemoteActivityRepository::class, function () {
            return new FranceTravailActivityDatasource();
        });

        $container->add(RemoteCompanyRepository::class, function () {
            return new DataGouvCompanyDatasource();
        });

        $container->add(RemoteZoneRepository::class, function () {
            return new GeoApiDatasource();
        });

        $container->add(RemotePersonRepository::class, function () {
            return new WikiApiDatasource();
        });

        $container->add(LocalPersonRepository::class, function () use ($container) {
            return new PersonMysqlDatasource($container->resolve(DatabaseGateway::class));
        });

        $container->add(LocalDomainRepository::class, function () use ($container) {
            return new DomainMysqlDatasource($container->resolve(DatabaseGateway::class));
        });
    }
}
