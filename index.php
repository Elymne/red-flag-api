<?php

use Domain\Usecases\Start;
use Infra\Di\BuilderContainer;
use Infra\Di\Container;

require "vendor/autoload.php";

BuilderContainer::injectAll();
Container::get()->resolve(Start::class)->perform();
