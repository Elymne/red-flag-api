<?php

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalPersonRepository;
use Throwable;

class InsertMessage extends Usecase
{
    private LocalPersonRepository $_localPersonRepository;

    public function __construct(LocalPersonRepository $localPersonRepository)
    {
        $this->_localPersonRepository = $localPersonRepository;
    }

    /**
     * @param InsertMessageParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // Check $params type.
            if (!isset($params) || !($params instanceof InsertMessageParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
            }

            // check that person exists.
            $person = $this->_localPersonRepository->findUnique($params->personID);
            if (!isset($person)) {
                return new Result(code: 400, data: "Action failure : the person doesn't exists.");
            }

            // Insert the new message.
            $this->_localPersonRepository->addMessage($params->personID, $params->message);

            // Success response.
            return new Result(code: 201, data: "Action success : new entry in message database.");
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
