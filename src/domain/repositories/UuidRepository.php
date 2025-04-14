<?php

namespace Domain\Repositories;

interface UuidRepository
{
    /**
     * Simply generate an uuid by using an external lib or making thta by hands.
     * 
     * @return string - An uuid v4
     */
    public function generate(): string;
}
