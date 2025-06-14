<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Domain\Repositories\RemoteActivityRepository;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Throwable;

/**
 * Usecase : find as many activities from remote Datasource.
 * The Remote Datasource will depend of dependencies sources.
 */
class FindActivities extends Usecase
{
    private RemoteActivityRepository $_remoteActivityRepository;

    public function __construct(RemoteActivityRepository $remoteActivityRepository)
    {
        $this->_remoteActivityRepository = $remoteActivityRepository;
    }

    /**
     * @param FindActivitiesParams $params
     * @return Result<Zone[]>
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params FindActivitiesParams.
            if (!isset($params) || !($params instanceof FindActivitiesParams)) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 400,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindActivities usecase ins't a FindActivitiesParams object.",
                        trace: [
                            "expected" => FindActivitiesParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Fetch all remotes activities.
            $activities = $this->_remoteActivityRepository->findMany(name: $params->name);

            // * Return activities.
            return new Result(
                response: new ApiResponse(
                    success: true,
                    code: 200,
                    data: $activities,
                    message: "Activities found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Activities returned",
                    file: __FILE__,
                ),
            );
        } catch (Throwable $err) {
            return new Result(
                response: new ApiResponse(
                    success: false,
                    code: 500,
                    message: "An internal error occured.",
                ),
                logData: new LogData(
                    type: LogData::CRITICAL,
                    message: "Action failure : Exception catch.",
                    trace: $err,
                    file: __FILE__,
                ),
            );
        }
    }
}
