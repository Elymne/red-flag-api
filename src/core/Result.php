<?php

declare(strict_types=1);

namespace Core;

use Domain\Gateways\LoggerGateway;

readonly class Result
{
    public function __construct(
        public int $code, // * http code response or 1/0.
        public LogData $logData, // * LogData Information.
        public ApiResponse|null $response = null, // * The response content. (what client see).
    ) {
        // * Create log message for each action from my server.
        $logger = Container::get()->resolve(LoggerGateway::class);
        if ($logData->type === LogData::INFO) $logger->info($logData);
        if ($logData->type === LogData::WARNING) $logger->warning($logData);
        if ($logData->type === LogData::ERROR) $logger->error($logData);
        if ($logData->type === LogData::CRITICAL) $logger->critical($logData);
    }
}
