<?php

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalPersonRepository;
use Throwable;

class FindPersons extends Usecase
{
    private LocalPersonRepository $_localPersonRepository;

    public function __construct(LocalPersonRepository $localPersonRepository)
    {
        $this->_localPersonRepository = $localPersonRepository;
    }

    /**
     * @param FindPersonsParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // Check $params type.
            if (!isset($params) || !($params instanceof FindPersonsParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a FindPersonsParams structure.");
            }
            // Search persons corresponding to theses.
            $persons = $this->_localPersonRepository->findMany(
                firstname: $params->firstname,
                lastname: $params->lastname,
                zonename: $params->zonename,
            );
            // Return persons from remotes.
            return new Result(200, $persons);
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
