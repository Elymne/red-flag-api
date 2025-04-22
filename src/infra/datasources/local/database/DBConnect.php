<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Gateways\DatabaseGateway;
use Infra\Datasources\Migrations;
use Exception;
use mysqli;

/**
 * Singleton class to manage SQL query from my database.
 */
class DBConnect implements DatabaseGateway
{
    private static ?DBConnect $_instance = null;
    private mysqli $_mysqli;

    private function __construct()
    {
        $this->_mysqli = new mysqli(
            hostname: $_ENV["DB_HOST"],
            database: $_ENV["DB_DATABASE"],
            username: $_ENV["DB_USERNAME"],
            password: $_ENV["DB_PWD"]
        );
        if ($this->_mysqli->connect_error) {
            throw $this->_mysqli->connect_error;
        }
    }

    public static function get(): DBConnect
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new DBConnect();
        }
        return self::$_instance;
    }

    public function getMysqli(): mysqli
    {
        if (!$this->_mysqli) {
            throw new Exception("DBConnect : Trying to use sql client but the value is not set.");
        }
        return $this->_mysqli;
    }

    public function runMigrations(): void
    {
        if (!$this->_mysqli) {
            throw new Exception("DBConnect : Trying to use sql client but the value is not set.");
        }
        Migrations::runMigration($this->_mysqli);
    }
}
