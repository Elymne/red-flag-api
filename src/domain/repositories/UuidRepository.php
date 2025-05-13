<?php

namespace Domain\Repositories;

interface UuidRepository
{
    /**
     * Simply generate an uuid string by using an external lib or making that by hands.
     * 
     * @return string - An uuid v4
     */
    public function generate(): string;

    /**
     * Simply generate bytes values of an uuid by using an external lib or making thta by hands.
     * 
     * @return string - An uuid v4
     */
    public function generateBytes(): string;

    /**
     * Convert uuid string to array of bytes (a string in php).
     * @return string - An uuid v4
     */
    public function toBytes(string $uuid): string;
}
