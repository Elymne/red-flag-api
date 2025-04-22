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
                    code: 400,
                    response: new ApiResponse(
                        success: false,
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
            $activities = $this->_remoteActivityRepository->findAll();

            // * Return activities.
            return new Result(
                code: 200,
                response: new ApiResponse(
                    success: true,
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
