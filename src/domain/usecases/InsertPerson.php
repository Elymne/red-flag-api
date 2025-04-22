<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Models\Person;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemoteActivityRepository;
use Domain\Repositories\RemoteCompanyRepository;
use Domain\Repositories\RemoteZoneRepository;
use Domain\Repositories\UuidRepository;
use Infra\Datasources\DBConnect;
use Throwable;

class InsertPerson extends Usecase
{
    private UuidRepository $_uuidRepository;
    private DBConnect $_db;
    private RemoteZoneRepository $_remoteZoneRepository;
    private RemoteCompanyRepository $_remoteCompanyRepository;
    private RemoteActivityRepository $_remoteActivityRepository;
    private LocalPersonRepository $_localPersonRepository;

    public function __construct(
        UuidRepository $uuidRepository,
        DBConnect $db,
        RemoteZoneRepository $remoteZoneRepository,
        RemoteCompanyRepository $remoteCompanyRepository,
        RemoteActivityRepository $remoteActivityRepository,
        LocalPersonRepository $localPersonRepository,
    ) {
        $this->_uuidRepository = $uuidRepository;
        $this->_db = $db;
        $this->_remoteZoneRepository = $remoteZoneRepository;
        $this->_remoteCompanyRepository = $remoteCompanyRepository;
        $this->_remoteActivityRepository = $remoteActivityRepository;
        $this->_localPersonRepository = $localPersonRepository;
    }

    /**
     * @param InsertPersonParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params type.
            if (!isset($params) || !($params instanceof InsertPersonParams)) {
                return new Result(
                    code: 400,
                    response: new ApiResponse(
                        success: false,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to InsertPerson usecase ins't a InsertPersonParams object.",
                        trace: [
                            "expected" => InsertPersonParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Start transaction
            $this->_db->getMysqli()->begin_transaction();

            // * Check that the zoneID exists.
            $zone = $this->_remoteZoneRepository->findUnique($params->zoneID);
            if (!isset($zone)) {
                return new Result(
                    code: 404,
                    response: new ApiResponse(
                        success: false,
                        message: "Zone not found.",
                    ),
                    logData: new LogData(
                        type: LogData::INFO,
                        message: "Action failure : Zone with id $params->zoneID not found.",
                        file: __FILE__,
                    ),
                );
            }

            // * Check that the companyID exists (if not null).
            /** @var Company|null */
            $company = null;
            if (isset($params->companyID)) {
                $company = $this->_remoteCompanyRepository->findUnique($params->companyID);
                if (!isset($company)) {
                    return new Result(
                        code: 404,
                        response: new ApiResponse(
                            success: false,
                            message: "Company not found.",
                        ),
                        logData: new LogData(
                            type: LogData::INFO,
                            message: "Action failure : Company with id $params->companyID not found.",
                            file: __FILE__,
                        ),
                    );
                }
            }

            // * Check that the activityID exists (if not null).
            /** @var Activity|null */
            $activity = null;
            if (isset($params->activityID)) {
                $activity = $this->_remoteActivityRepository->findUnique($params->activityID);
                if (!isset($activity)) {
                    return new Result(
                        code: 404,
                        response: new ApiResponse(
                            success: false,
                            message: "Activity not found.",
                        ),
                        logData: new LogData(
                            type: LogData::INFO,
                            message: "Action failure : Activity with id $params->activityID not found.",
                            file: __FILE__,
                        ),
                    );
                }
            }

            // * Check that user does not exists in database. If it's the case, we just send a 406 response.
            if ($this->_localPersonRepository->doesExists(
                firstname: strtolower($params->firstname),
                lastname: strtolower($params->lastname),
                birthDate: $params->birthDate,
                zoneID: $params->zoneID,
                companyID: $params->companyID,
                activityID: $params->activityID,
            )) {
                return new Result(
                    code: 406,
                    response: new ApiResponse(
                        success: false,
                        message: "trying to duplicate person.",
                    ),
                    logData: new LogData(
                        type: LogData::INFO,
                        message: "Action failure : Trying to duplicate person.",
                        trace: $params,
                        file: __FILE__,
                    ),
                );
            }

            // * Insert the new Person to database.
            $personToCreate = new Person(
                ID: $this->_uuidRepository->generateBytes(),
                firstname: strtolower($params->firstname),
                lastname: strtolower($params->lastname),
                birthDate: $params->birthDate,
                zone: $zone,
                activity: $activity,
                company: $company,
                createdAt: time(),
                links: [], // * No link On creation.
                portrait: null, // * Data from Remote.
                description: null, // * Data from Remote.
                pseudonym: null, // * Data from Remote.
            );
            $this->_localPersonRepository->createOne($personToCreate);

            // * Commit transaction.
            $this->_db->getMysqli()->commit();

            // * Success response.
            return new Result(
                code: 201,
                response: new ApiResponse(
                    success: true,
                    data: $personToCreate,
                    message: "Person created.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Person created with success.",
                    trace: $personToCreate,
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
