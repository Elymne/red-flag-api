<?php

declare(strict_types=1);

namespace Core;

class LogData
{
    public const INTERNAL = 0;
    public const INFO = 1;
    public const WARNING = 10;
    public const ERROR = 100;
    public const CRITICAL = 1000;

    public function __construct(
        public int $type,
        public string $message,
        public mixed $trace = null, // * Nullable.
        public string|null $file = null, // * Nullable.
        public int|null $line = null, // * Nullable.
    ) {}
}
