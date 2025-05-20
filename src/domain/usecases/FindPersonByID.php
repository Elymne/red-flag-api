<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Models\PersonDetailed;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemotePersonRepository;
use Domain\Repositories\UuidRepository;
use Throwable;

/**
 * Usecase : Find a single person given is ID (each ID are unique).
 */
class FindPersonByID extends Usecase
{
    private UuidRepository $_uuidRepository;
    private LocalPersonRepository $_localPersonRepository;
    private RemotePersonRepository $_remotePersonRepository;

    public function __construct(UuidRepository $uuidRepository, LocalPersonRepository $localPersonRepository, RemotePersonRepository $remotePersonRepository)
    {
        $this->_uuidRepository = $uuidRepository;
        $this->_localPersonRepository = $localPersonRepository;
        $this->_remotePersonRepository = $remotePersonRepository;
    }

    /**
     * @param FindLocalPersonParams $params
     * @return Result<PersonDetailed>  
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params type.
            if (!isset($params) || !($params instanceof FindPersonByIDParams)) {
                return new Result(
                    code: 400,
                    response: new ApiResponse(
                        success: false,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindPersonByID usecase ins't a FindPersonByIDParams object.",
                        trace: [
                            "expected" => FindPersonByIDParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Search persons corresponding to theses.
            $uuidBytes = $this->_uuidRepository->toBytes($params->ID);
            $person = $this->_localPersonRepository->findUnique($uuidBytes);

            // * Check the value.
            if (!isset($person)) {
                return new Result(
                    code: 404,
                    response: new ApiResponse(
                        success: false,
                        message: "Person not found.",
                    ),
                    logData: new LogData(
                        type: LogData::INFO,
                        message: "Action failure : Person with id $params->ID not found.",
                        file: __FILE__,
                    ),
                );
            }

            // * Fetch the remote data.
            $additionnalData = $this->_remotePersonRepository->findRemoteData(
                firstname: $person->firstname,
                lastname: $person->lastname,
            );

            // * Result return with Person + additionnal data.
            return new Result(
                code: 200,
                response: new ApiResponse(
                    success: true,
                    data: $additionnalData === null ? $person : $person->copyWith(
                        portrait: $additionnalData->portrait,
                        description: $additionnalData->description,
                    ),
                    message: "Person found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Person $params->ID fetched with success.",
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
