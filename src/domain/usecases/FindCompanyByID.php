<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Repositories\RemoteCompanyRepository;
use Throwable;

/**
 * Usecase : find as many companies from remote Datasource.
 * The Remote Datasource will depend of dependencies sources.
 */
class FindCompanyByID extends Usecase
{
    private RemoteCompanyRepository $_remoteCompanyRepository;

    public function __construct(RemoteCompanyRepository $remoteCompanyRepository)
    {
        $this->_remoteCompanyRepository = $remoteCompanyRepository;
    }

    /**
     * @param FindCompaniesParams $params
     * @return Result<Zone[]>
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params FindCompaniesParams.
            if (!isset($params) || !($params instanceof FindCompanyByIDParams)) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 400,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindCompanyByID usecase ins't a FindCompanyByIDParams object.",
                        trace: [
                            "expected" => FindCompanyByIDParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Search remote company that correspond to params.
            $company = $this->_remoteCompanyRepository->findUnique(ID: $params->ID);


            // * Check the value.
            if (!isset($company)) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 404,
                        message: "Company not found.",
                    ),
                    logData: new LogData(
                        type: LogData::INFO,
                        message: "Action failure : Company with id $params->ID not found.",
                        file: __FILE__,
                    ),
                );
            }

            // * Return companies.
            return new Result(
                response: new ApiResponse(
                    success: true,
                    code: 200,
                    data: $company,
                    message: "Company found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Company $params->ID fetched with success.",
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
