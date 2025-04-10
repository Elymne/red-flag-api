<?php

use Domain\Usecases\Start;
use Infra\Di\BuilderContainer;
use Infra\Di\Container;
use Infra\Env\Env;

require "vendor/autoload.php";

Env::load();

BuilderContainer::injectAll();

$start = Container::get()->resolve(Start::class);
$start->perform();
