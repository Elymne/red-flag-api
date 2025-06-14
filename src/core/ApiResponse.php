<?php

declare(strict_types=1);

namespace Core;

class ApiResponse
{
    public function __construct(
        public bool $success,
        public int $code,
        public string $message,
        public mixed $data = null,
    ) {}
}
