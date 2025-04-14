<?php

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalPersonRepository;
use Throwable;

class FindUniquePerson extends Usecase
{
    private LocalPersonRepository $_localPersonRepository;

    public function __construct(LocalPersonRepository $localPersonRepository)
    {
        $this->_localPersonRepository = $localPersonRepository;
    }

    /**
     * @param FindUniquePersonParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // Check $params type.
            if (!isset($params) || !($params instanceof FindUniquePersonParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a FindPersonsParams structure.");
            }
            // Search persons corresponding to theses.
            $person = $this->_localPersonRepository->findUnique($params->id);
            // Check the value.
            if (!isset($person)) {
                return new Result(code: 404, data: "Action failure : this person does not exists.");
            }
            // Return persons from remotes.
            return new Result(200, $person);
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
