<?php

use Domain\Usecases\LoadEnv;
use Domain\Usecases\Run;
use Core\Container;
use Core\Result;

require "vendor/autoload.php";

// * Exemple basique de route pour exposer swagger.json
if ($_SERVER["REQUEST_URI"] === "/swagger.json") {
    header("Content-Type: application/json");
    readfile(__DIR__ . "/public/swagger.json");
    exit;
}

// * Define consts.
define("ROOT_PATH", __DIR__);

// * Use DI container.
Container::injectAll();

// * Load env file.
/** @var Result */
$loadEnvResult = Container::get()->resolve(LoadEnv::class)->perform();
if ($loadEnvResult->code == 1) exit;


// * Run server.
/** @var Result */
$runResult = Container::get()->resolve(Run::class)->perform();
if ($runResult->code == 1) exit;
