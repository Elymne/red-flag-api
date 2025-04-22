<?php

namespace Domain\Gateways;

interface DatabaseGateway
{
    public function runMigrations(): void;
}
