<?php

use Infra\Di\BuilderContainer;
use Infra\Di\Container;
use Infra\Env\Env;
use Domain\Usecases\Run;
use Core\Result;

require "vendor/autoload.php";

// * Load env variables.
Env::load();

// * Use DI container.
BuilderContainer::injectAll();

// * Run server.
/** @var Result */
$result = Container::get()->resolve(Run::class)->perform();
if ($result->code == 1) {
    print_r($result->data);
    exit;
}
