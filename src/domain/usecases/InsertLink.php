<?php

declare(strict_types=1);

namespace Domain\Usecases;

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
            // Check $params type.
            if (!isset($params) || !($params instanceof InsertLinkParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
            }
            $parsedUrl = parse_url($params->link);
            // Check that the URL uses HTTPS.
            if ($parsedUrl["scheme"] != 'https') {
                return new Result(code: 400, data: "Action failure : the provided link is not a valid HTTPS URL.");
            }
            // Extract the domain name.
            if (!isset($parsedUrl['host'])) {
                return new Result(code: 400, data: "Action failure : unable to extract domain name from the provided URL.");
            }
            // Check that the domain name exists in our filter.
            $requestedDomain = $this->_localDomainRepository->findUnique($parsedUrl['host']);
            if (!isset($requestedDomain)) {
                return new Result(code: 400, data: "Action failure : the domain name is not accepted.");
            }
            // check that person exists.
            $person = $this->_localPersonRepository->findUnique($params->personID);
            if (!isset($person)) {
                return new Result(code: 400, data: "Action failure : the person does not exists.");
            }
            // Insert the new message.
            $this->_localPersonRepository->addLink($params->personID, new Link(
                $this->_uuidRepository->generate(),
                $params->link,
                createdAt: time()
            ));
            // Success response.
            return new Result(code: 201, data: "Action success : new entry in message database.");
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
