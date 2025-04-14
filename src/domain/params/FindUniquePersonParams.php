<?php

namespace Domain\Usecases;

readonly class FindUniquePersonParams
{
    public function __construct(public string $id) {}
}
