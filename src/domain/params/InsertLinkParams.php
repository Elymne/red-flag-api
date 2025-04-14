<?php

namespace Domain\Usecases;

readonly class InsertLinkParams
{
    public function __construct(public string $personID, public string $link) {}
}
