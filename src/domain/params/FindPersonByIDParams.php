<?php

namespace Domain\Usecases;

readonly class FindPersonByIDParams
{
    public function __construct(
        public string $ID
    ) {}
}
