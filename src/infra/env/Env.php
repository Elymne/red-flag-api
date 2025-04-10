<?php

namespace Infra\Env;

use Domain\Gateways\EnvGateway;

class Env implements EnvGateway
{
    public function load(): void
    {
        $lines = file(".env");
        foreach ($lines as $line) {
            [$key, $value] = explode("=", $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv(sprintf("%s=%s", $key, $value));
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
