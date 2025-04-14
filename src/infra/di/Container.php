<?php

declare(strict_types=1);

namespace Infra\Di;

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

    /**
     * Add a new value that will be injected.
     * 
     * @param string $key The name of the class or key to get the requested object.
     * @param callable $builder The function that will run and return the requested object.
     * The builder can have $args params if needed.
     */
    public function add(string $key, callable $builder): void
    {
        $this->_bindings[$key] = $builder;
    }

    /**
     * Fetch the class asked in arg.
     * 
     * @param string $key The name of the value requested.
     * @param array $args Arguments need by the value that we need.
     * @return mixed The object or value requested.
     */
    public function resolve(string $key, array $args = []): mixed
    {
        if (!isset($this->_bindings[$key])) {
            throw new Exception("Class from key $key not found");
        }

        return $this->_bindings[$key]($args);
    }
}
