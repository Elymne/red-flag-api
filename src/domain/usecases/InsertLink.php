<?php

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalPersonRepository;
use Throwable;

class InsertLink extends Usecase
{
    private LocalPersonRepository $_localPersonRepository;

    public function __construct(LocalPersonRepository $localPersonRepository)
    {
        $this->_localPersonRepository = $localPersonRepository;
    }

    /**
     * @param InsertLinkParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // Check $params type.
            if (!isset($params) || !($params instanceof InsertLinkParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
            }

            // Check that the URL uses HTTPS.
            if (parse_url($params->link, PHP_URL_SCHEME) !== 'https') {
                return new Result(code: 400, data: "Action failure : the provided link is not a valid HTTPS URL.");
            }

            //TODO : Database list domain.
            $accepted_domains = [
                "www.mediapart.fr",
            ];

            // Extract the domain name.
            $parsedUrl = parse_url($params->link);
            if (!isset($parsedUrl['host'])) {
                return new Result(code: 400, data: "Action failure : unable to extract domain name from the provided URL.");
            }

            // Check that the domain name exists in our filter.
            $domainName = $parsedUrl['host'];
            if (!in_array($domainName, $accepted_domains)) {
                return new Result(code: 400, data: "Action failure : the domain name is not accepted.");
            }

            // check that person exists.
            $person = $this->_localPersonRepository->findUnique($params->personID);
            if (!isset($person)) {
                return new Result(code: 400, data: "Action failure : the person does not exists.");
            }

            // Insert the new message.
            $this->_localPersonRepository->addLink($params->personID, $params->link);

            // Success response.
            return new Result(code: 201, data: "Action success : new entry in message database.");
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
