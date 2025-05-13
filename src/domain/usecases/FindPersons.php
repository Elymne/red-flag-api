<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemotePersonRepository;
use Throwable;

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
     * @uses LocalPersonRepository
     * @uses RemotePersonRepository
     * 
     * @param FindPersonsParams $params
     * @return Result<Person[]>  
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params type.
            if (!isset($params) || !($params instanceof FindPersonsParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a FindPersonsParams structure.");
            }
            // * Search persons corresponding to theses.
            $persons = $this->_localPersonRepository->findMany(
                firstname: $params->firstname,
                lastname: $params->lastname,
                birthday: $params->birthday,
                jobname: $params->jobname,
                zonename: $params->zonename,
            );
            /** @var Person[] */
            $personsWithAdditionnalData = [];
            // * Add additionnal data to each person. (image…)
            for ($i = 0; $i < count($persons); $i++) {
                $person = $persons[$i];
                $fullname = $person->firstName . "_" . $person->lastName;
                $additionnalData = $this->_remotePersonRepository->findAdditionalData($fullname);
                // * Check that data exists, else just pass.
                if (isset($additionnalData)) {
                    array_push($personsWithAdditionnalData, $person->copyWith(portrait: $additionnalData->portrait));
                } else {
                    array_push($personsWithAdditionnalData, $person);
                }
            }
            // * Return persons from remotes.
            return new Result(200, $personsWithAdditionnalData);
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
