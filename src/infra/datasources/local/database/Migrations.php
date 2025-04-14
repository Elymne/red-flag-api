<?php

namespace Infra\Datasources;

use mysqli;

class Migrations
{
    public static function runMigration(mysqli $mysqli): void
    {
        self::generateTables($mysqli);
        self::generateFunctions($mysqli);
    }

    private static function generateTables(mysqli $mysqli): void
    {
        /** @var string */
        $query = "
            CREATE TABLE IF NOT EXISTS domain(
                value VARCHAR(250) UNIQUE NOT NULL,
             
                CONSTRAINT pk_domain PRIMARY KEY (value)
            );
            
            CREATE TABLE IF NOT EXISTS zone(
                id VARCHAR(10) UNIQUE NOT NULL,
                name VARCHAR(250) NOT NULL,
             
                CONSTRAINT pk_zone PRIMARY KEY (id)
            );
            
            CREATE TABLE IF NOT EXISTS person(
                id BINARY(16) UNIQUE NOT NULL,
                first_name VARCHAR(250) NOT NULL,
                last_name VARCHAR(250) NOT NULL,
                id_zone VARCHAR(250) NOT NULL,

                created_at INT(11) NOT NULL,
                updated_at INT(11),

                CONSTRAINT pk_person PRIMARY KEY (id),
                CONSTRAINT fk_person_zone FOREIGN KEY (id_zone) REFERENCES zone(id)
            );

            CREATE TABLE IF NOT EXISTS link(
                id BINARY(16) UNIQUE NOT NULL,
                value VARCHAR(250) UNIQUE NOT NULL,

                created_at INT(11) NOT NULL,
                updated_at INT(11),

                CONSTRAINT pk_link PRIMARY KEY (id)
            );

            CREATE TABLE IF NOT EXISTS message(
                id BINARY(16) UNIQUE NOT NULL,
                value LONGTEXT UNIQUE NOT NULL,

                created_at INT(11) NOT NULL,
                updated_at INT(11),

                CONSTRAINT pk_message PRIMARY KEY (id)
            );

            CREATE TABLE IF NOT EXISTS person_link(
                id_person BINARY(16) UNIQUE NOT NULL,
                id_link BINARY(16) UNIQUE NOT NULL,

                CONSTRAINT pk_person_link PRIMARY KEY (id_person, id_link)
                CONSTRAINT fk_person_link_person FOREIGN KEY (id_person) REFERENCES person(id)
                CONSTRAINT fk_person_link_link FOREIGN KEY (id_link) REFERENCES link(id)
            );

            CREATE TABLE IF NOT EXISTS person_message(
                id_person BINARY(16) UNIQUE NOT NULL,
                id_message BINARY(16) UNIQUE NOT NULL,

                CONSTRAINT pk_person_message PRIMARY KEY (id_person, id_message)
                CONSTRAINT fk_person_message_person FOREIGN KEY (id_person) REFERENCES person(id)
                CONSTRAINT fk_person_message_message FOREIGN KEY (id_message) REFERENCES message(id)
            );";

        $mysqli->multi_query($query);
    }

    private static function generateFunctions(mysqli $mysqli): void {}
}
