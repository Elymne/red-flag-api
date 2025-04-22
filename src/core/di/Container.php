<?php

declare(strict_types=1);

namespace Core;

use Exception;

/**
 * Simple Custom Container for IoC.
 * This is a class singleton.
 * I need a unique container that will be used by all my classes.
 */
class Container
{
    private static Container|null $_instance = null;

    /**
     * @return Container Singleton object access.
     */
    public static function get(): Container
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Container();
        }
        return self::$_instance;
    }

    private function __construct() {}

    /**
     * List of callback function that will run when builded class are asked by something. 
     * @var callable[]
     */
    private array $_bindings = [];

    public function add(string $key, callable $builder): void
    {
        $this->_bindings[$key] = $builder;
    }

    public function resolve(string $key, array $args = []): mixed
    {
        if (!isset($this->_bindings[$key])) {
            throw new Exception("Class from key $key not found");
        }

        return $this->_bindings[$key]($args);
    }

    public static function injectAll(): void
    {
        $container = Container::get();
        BuildGateways::inject($container);
        BuildRepositories::inject($container);
        BuildUsecases::inject($container);
    }
}
