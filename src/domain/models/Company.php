<?php

namespace Domain\Models;

/**
 * Data structure about Link/Article about a person.
 */
readonly class Company
{
    public function __construct(
        /** @var string */
        public string $ID, // siren

        /** @var string */
        public string $name, // nom_complet

        /** @var string */
        public string $address, // geo_adresse
    ) {}
}
