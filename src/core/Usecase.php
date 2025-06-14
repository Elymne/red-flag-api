<?php

declare(strict_types=1);

namespace Core;

use Domain\Gateways\Logger;
use Infra\Di\Container;

/**
 * Dictate how usecase (Logic between infra database,remote access, librairies etc) should working.
 * A usecase always return a Result object.
 * Params can be anything, you can enforce params by using comment typo, ex : @param UsecaseParams $params
 */
abstract class Usecase
{
    abstract public function perform(mixed $params): Result;
}
