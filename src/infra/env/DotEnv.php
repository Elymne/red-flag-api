<?php

declare(strict_types=1);

namespace Infra\Env;

use Domain\Gateways\EnvGateway;
use Dotenv\Dotenv as DotenvDotenv;

class DotEnv implements EnvGateway
{
    public function load(): void
    {
        $dotenv = DotenvDotenv::createImmutable(ROOT_PATH);
        $dotenv->load();
    }
}
