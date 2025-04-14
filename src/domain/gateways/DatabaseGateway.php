<?php

namespace Domain\Gateways;

interface DatabaseGateway
{
    function runMigrations(): void;
}
