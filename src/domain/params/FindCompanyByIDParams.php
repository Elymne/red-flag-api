<?php

namespace Domain\Usecases;

readonly class FindCompanyByIDParams
{
    public function __construct(
        public string $ID,
    ) {}
}
