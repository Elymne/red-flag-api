<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemotePersonRepository;
use Throwable;

/**
 * Usecase : Find as many persons as possible given the params from local database.
 */
class FindPersons extends Usecase
{
    private LocalPersonRepository $_localPersonRepository;
    private RemotePersonRepository $_remotePersonRepository;

    public function __construct(LocalPersonRepository $localPersonRepository, RemotePersonRepository $remotePersonRepository)
    {
        $this->_localPersonRepository = $localPersonRepository;
        $this->_remotePersonRepository = $remotePersonRepository;
    }

    /**
     * @param FindPersonsParams $params
     * @return Result<Person[]>  
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params FindPersonsParams.
            if (!isset($params) || !($params instanceof FindPersonsParams)) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 400,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindPersons usecase ins't a FindPersonsParams object.",
                        trace: [
                            "expected" => FindPersonsParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Search persons corresponding to params.
            // TODO On change tout ça, need faire mes remotes datasourcve tho.
            $persons = $this->_localPersonRepository->findMany(
                firstname: $params->firstname,
                lastname: $params->lastname,
                birthDate: $params->birthDate,
                activityID: $params->activityID,
                companyID: $params->companyID,
                zoneID: $params->zoneID,
            );

            // * Empty result.
            if (count($persons) === 0) {
                return new Result(
                    response: new ApiResponse(
                        success: true,
                        code: 200,
                        data: $persons,
                        message: "No data.",
                    ),
                    logData: new LogData(
                        type: LogData::INFO,
                        message: "Action success : No data.",
                        trace: $params,
                        file: __FILE__,
                    ),
                );
            }

            // * Initial persons list but with more data fetched from Remote Datasource.
            /** @var Person[] */
            $personsDetailed = [];

            // * Fetch and add additionnal data for each person fetched from our database.
            for ($i = 0; $i < count($persons); $i++) {
                $person = $persons[$i];

                // * Fetch the remote data.
                $additionnalData = $this->_remotePersonRepository->findRemoteData(
                    firstname: $person->firstname,
                    lastname: $person->lastname,
                );

                // * Check that data exists, else just pass.
                if (isset($additionnalData)) {
                    array_push($personsDetailed, $person->copyWith(portrait: $additionnalData->portrait));
                    continue;
                }

                // * Else simply push the person without any information.
                array_push($personsDetailed, $person);
            }

            // * Return persons from remotes.
            return new Result(
                response: new ApiResponse(
                    success: true,
                    code: 200,
                    data: $personsDetailed,
                    message: "Persons found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Persons fetched with success.",
                    trace: $params,
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
                    message: "Action failure : Unexpected error occured.",
                    trace: $err,
                    file: __FILE__,
                ),
            );
        }
    }
}
