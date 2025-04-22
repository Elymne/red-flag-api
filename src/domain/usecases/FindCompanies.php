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
class FindCompanies extends Usecase
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
            if (!isset($params) || !($params instanceof FindCompaniesParams)) {
                return new Result(
                    code: 400,
                    response: new ApiResponse(
                        success: false,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to FindCompanies usecase ins't a FindCompaniesParams object.",
                        trace: [
                            "expected" => FindCompaniesParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Search remote companies that correspond to params.
            $companies = $this->_remoteCompanyRepository->findMany(name: $params->name);

            // * Return companies.
            return new Result(
                code: 200,
                response: new ApiResponse(
                    success: true,
                    data: $companies,
                    message: "Companies found.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Companies fetched with success.",
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
