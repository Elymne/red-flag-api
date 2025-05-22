<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\ApiResponse;
use Core\LogData;
use Core\Result;
use Core\Usecase;
use Domain\Models\Link;
use Domain\Repositories\LocalDomainRepository;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\UuidRepository;
use Throwable;

class InsertLink extends Usecase
{
    private UuidRepository $_uuidRepository;
    private LocalPersonRepository $_localPersonRepository;
    private LocalDomainRepository $_localDomainRepository;

    public function __construct(
        UuidRepository $uuidRepository,
        LocalPersonRepository $localPersonRepository,
        LocalDomainRepository $localDomainRepository
    ) {
        $this->_uuidRepository = $uuidRepository;
        $this->_localPersonRepository = $localPersonRepository;
        $this->_localDomainRepository = $localDomainRepository;
    }

    /**
     * @param InsertLinkParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params type.
            if (!isset($params) || !($params instanceof InsertLinkParams)) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 400,
                        message: "An internal error occured.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Argument provided to InsertLink usecase ins't a InsertLinkParams object.",
                        trace: [
                            "expected" => InsertLinkParams::class,
                            "given" => gettype($params),
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Parse URL.
            $parsedUrl = parse_url($params->source);

            // * Check that the URL uses HTTPS.
            if ($parsedUrl["scheme"] != "https") {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 406,
                        message: "Invalid https structure.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Invalid https structure provided.",
                        trace: [
                            "source" => $params->source,
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Extract the domain name.
            if (!isset($parsedUrl["host"])) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 406,
                        message: "Problems with hostname.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Unable to extract hostname : $params->source.",
                        trace: [
                            "source" => $params->source,
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Check that the domain name exists in our filter.
            if (!$this->_localDomainRepository->doesExists($parsedUrl["host"])) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 406,
                        message: "Not Acceptable hostname.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Hostname invalid : $params->source.",
                        trace: [
                            "hostname" => $parsedUrl["host"],
                        ],
                        file: __FILE__,
                    ),
                );
            }

            // * Check that person exists.
            $personUUID = $this->_uuidRepository->toBytes($params->personID);
            $person = $this->_localPersonRepository->findUnique($personUUID);
            if (!isset($person)) {
                return new Result(
                    response: new ApiResponse(
                        success: false,
                        code: 406,
                        message: "Person not found.",
                    ),
                    logData: new LogData(
                        type: LogData::ERROR,
                        message: "Action failure : Person with id $params->personID not found.",
                        file: __FILE__,
                    ),
                );
            }

            // * Check that the same article isn't existing in database.
            foreach ($person->links as $link) {
                // * Cast Link object.
                $link = $link instanceof Link ? $link : null;
                // * Should not happen but if link isn't typed as Link.
                if ($link == null) continue;
                if ($link->source === $params->source) {
                    return new Result(
                        response: new ApiResponse(
                            success: false,
                            code: 400,
                            message: "Trying to duplicate link.",
                        ),
                        logData: new LogData(
                            type: LogData::INFO,
                            message: "Action failure : Duplicate link for person $params->personID.",
                            file: __FILE__,
                        ),
                    );
                }
            }

            // * Insert the new message.
            $this->_localPersonRepository->addLink(
                $personUUID,
                new Link(
                    ID: $this->_uuidRepository->generateBytes(),
                    source: $params->source,
                    createdAt: time(),
                )
            );

            // * Success response.
            return new Result(
                response: new ApiResponse(
                    success: true,
                    code: 201,
                    message: "Link created.",
                ),
                logData: new LogData(
                    type: LogData::INFO,
                    message: "Action success : Link created with success.",
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
