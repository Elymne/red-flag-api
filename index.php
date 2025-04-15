<?php

use Infra\Di\BuilderContainer;
use Infra\Di\Container;
use Infra\Env\Env;
use Domain\Usecases\Run;
use Core\Result;

require "vendor/autoload.php";

Env::load();

BuilderContainer::injectAll();

/** @var Result */
$result = Container::get()->resolve(Run::class)->perform();
if ($result->code == 1) {
    print_r($result->data);
    exit;
}
