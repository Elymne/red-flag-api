<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Domain\Repositories\RemoteActivityRepository;
use Core\Result;
use Core\Usecase;
use Throwable;

/**
 * Usecase : find as many activities from remote Datasource.
 * The Remote Datasource will depend of dependencies sources.
 */
class FindActivityByID extends Usecase
{
    private RemoteActivityRepository $_remoteActivityRepository;

    public function __construct(RemoteActivityRepository $remoteActivityRepository)
    {
        $this->_remoteActivityRepository = $remoteActivityRepository;
    }

    /**
     * @param FindActivityByIDParams $params
     * @return Result<Zone[]>
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params FindActivitiesParams.
            if (!isset($params) || !($params instanceof FindActivityByIDParams)) {
                return new Result(
                    code: 400,
                    response: new ApiResponse(
                        success: false,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindActivityByID usecase ins't a FindActivityByIDParams object.",
                        trace: [
                            "expected" => FindActivityByIDParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Find the unique activity given his ID.
            $activity = $this->_remoteActivityRepository->findUnique($params->ID);

            // * Check the value.
            if (!isset($activity)) {
                return new Result(
                    code: 404,
                    response: new ApiResponse(
                        success: false,
                        message: "Activity not found.",
                    ),
                    logData: new LogData(
                        type: LogData::INFO,
                        message: "Action failure : Activity with id $params->ID not found.",
                        file: __FILE__,
                    ),
                );
            }

            // * Return the activity.
            return new Result(
                code: 200,
                response: new ApiResponse(
                    success: true,
                    data: $activity,
                    message: "Activity found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Activty $params->ID fetched with success.",
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
