<?php

namespace Infra\Datasources;

use mysqli;

class Migrations
{
    public static function runMigration(mysqli $mysqli): void
    {
        self::generateTables($mysqli);
    }

    private static function generateTables(mysqli $mysqli): void
    {
        /** @var string */
        $query = "
            CREATE TABLE IF NOT EXISTS domain(
                value VARCHAR(250) UNIQUE NOT NULL,
             
                CONSTRAINT pk_domain PRIMARY KEY (value)
            );
            
            CREATE TABLE IF NOT EXISTS person(
                id BINARY(16) UNIQUE NOT NULL,
                first_name VARCHAR(250) NOT NULL,
                last_name VARCHAR(250) NOT NULL,
                birth_date INT(11) NOT NULL,
                
                id_zone VARCHAR(250) NOT NULL,
                id_company VARCHAR(250) NOT NULL,
                id_activity VARCHAR(250) NOT NULL,

                created_at INT(11) NOT NULL,

                CONSTRAINT pk_person PRIMARY KEY (id)
            );

            CREATE TABLE IF NOT EXISTS link(
                id BINARY(16) UNIQUE NOT NULL,
                source VARCHAR(250) UNIQUE NOT NULL,

                created_at INT(11) NOT NULL,

                id_person BINARY(16) NOT NULL,

                CONSTRAINT pk_link PRIMARY KEY (id),
                CONSTRAINT fk_link_person FOREIGN KEY (id_person) REFERENCES person(id)
            );
            ";

        $mysqli->multi_query($query);
    }
}
