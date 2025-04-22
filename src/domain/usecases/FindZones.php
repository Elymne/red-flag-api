<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Repositories\RemoteZoneRepository;
use Throwable;

/**
 * Usecase : find as many zone as possible from remote.
 * The Remote Datasource will depend of dependencies sources.
 */
class FindZones extends Usecase
{
    private RemoteZoneRepository $_remoteZoneRepository;

    public function __construct(RemoteZoneRepository $remoteZoneRepository)
    {
        $this->_remoteZoneRepository = $remoteZoneRepository;
    }

    /**
     * @param FindZonesParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params type.
            if (!isset($params) || !($params instanceof FindZonesParams)) {
                return new Result(
                    code: 400,
                    response: new ApiResponse(
                        success: false,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindZones usecase ins't a FindZonesParams object.",
                        trace: [
                            "expected" => FindZonesParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Search remote zone.
            $zones = $this->_remoteZoneRepository->findMany(name: $params->name);

            // * Return zones from remotes.
            return new Result(
                code: 200,
                response: new ApiResponse(
                    success: true,
                    data: $zones,
                    message: "Zones found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Zones fetched with success.",
                    trace: $params,
                    file: __FILE__,
                ),
            );
        } catch (Throwable $err) {
            return new Result(
                code: 500,
                response: new ApiResponse(
                    success: false,
                    message: "An internal error occured.",
                ),
                logData: new LogData(
                    type: LogData::CRITICAL,
                    message: "Action failure : Unexpected error occured.",
                    trace: $err,
                    file: __FILE__,
                ),
            );
        }
    }
}
