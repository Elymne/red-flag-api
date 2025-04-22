<?php

declare(strict_types=1);

namespace Core;

use Domain\Gateways\DatabaseGateway;
use Domain\Gateways\EnvGateway;
use Domain\Gateways\RouterGateway;
use Domain\Repositories\LocalDomainRepository;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemoteActivityRepository;
use Domain\Repositories\RemoteCompanyRepository;
use Domain\Repositories\RemotePersonRepository;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Repositories\UuidRepository;
use Domain\Usecases\FindActivities;
use Domain\Usecases\FindActivityByID;
use Domain\Usecases\FindCompanies;
use Domain\Usecases\FindCompanyByID;
use Domain\Usecases\FindPersonByID;
use Domain\Usecases\FindPersons;
use Domain\Usecases\FindZones;
use Domain\Usecases\InsertLink;
use Domain\Usecases\InsertPerson;
use Domain\Usecases\LoadEnv;
use Domain\Usecases\Run;
use Domain\Usecases\RunMigrations;

class BuildUsecases
{
    public static function inject(Container $container)
    {
        $container->add(LoadEnv::class, function () use ($container): LoadEnv {
            return new LoadEnv($container->resolve(EnvGateway::class));
        });

        $container->add(Run::class, function () use ($container): Run {
            return new Run($container->resolve(RouterGateway::class));
        });

        $container->add(RunMigrations::class, function () use ($container): RunMigrations {
            return new RunMigrations($container->resolve(DatabaseGateway::class));
        });

        $container->add(FindPersons::class, function () use ($container): FindPersons {
            return new FindPersons(
                $container->resolve(LocalPersonRepository::class),
                $container->resolve(RemotePersonRepository::class)
            );
        });

        $container->add(FindPersonByID::class, function () use ($container): FindPersonByID {
            return new FindPersonByID(
                $container->resolve(UuidRepository::class),
                $container->resolve(LocalPersonRepository::class),
                $container->resolve(RemotePersonRepository::class),
            );
        });

        $container->add(FindZones::class, function () use ($container): FindZones {
            return new FindZones($container->resolve(RemoteZoneRepository::class));
        });

        $container->add(FindActivities::class, function () use ($container): FindActivities {
            return new FindActivities($container->resolve(RemoteActivityRepository::class));
        });

        $container->add(FindActivityByID::class, function () use ($container): FindActivityByID {
            return new FindActivityByID($container->resolve(RemoteActivityRepository::class));
        });

        $container->add(FindCompanies::class, function () use ($container): FindCompanies {
            return new FindCompanies($container->resolve(RemoteCompanyRepository::class));
        });

        $container->add(FindCompanyByID::class, function () use ($container): FindCompanyByID {
            return new FindCompanyByID($container->resolve(RemoteCompanyRepository::class));
        });

        $container->add(InsertLink::class, function () use ($container): InsertLink {
            return new InsertLink(
                $container->resolve(UuidRepository::class),
                $container->resolve(LocalPersonRepository::class),
                $container->resolve(LocalDomainRepository::class),
            );
        });

        $container->add(InsertPerson::class, function () use ($container): InsertPerson {
            return new InsertPerson(
                uuidRepository: $container->resolve(UuidRepository::class),
                db: $container->resolve(DatabaseGateway::class),
                localPersonRepository: $container->resolve(LocalPersonRepository::class),
                remoteZoneRepository: $container->resolve(RemoteZoneRepository::class),
                remoteCompanyRepository: $container->resolve(RemoteCompanyRepository::class),
                remoteActivityRepository: $container->resolve(RemoteActivityRepository::class),
            );
        });
    }
}
