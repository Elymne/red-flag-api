<?php

namespace Domain\Usecases;

readonly class FindCompaniesParams
{
    public function __construct(
        public string $name,
    ) {}
}
