<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Models\PersonDetailed;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemotePersonRepository;
use Throwable;

class FindUniquePerson extends Usecase
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
     * @param FindUniquePersonParams $params
     * @return Result<PersonDetailed>  
     */
    public function perform(mixed $params): Result
    {
        try {
            // * Check $params type.
            if (!isset($params) || !($params instanceof FindUniquePersonParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a FindUniquePersonParams structure.");
            }
            // * Search persons corresponding to theses.
            $person = $this->_localPersonRepository->findUnique($params->id);
            // * Check the value.
            if (!isset($person)) {
                return new Result(code: 404, data: "Action failure : this person does not exists.");
            }
            // * Find additionnal data from remote person repository.
            $fullname = $person->firstName . "_" . $person->lastName;
            $additionnalData = $this->_remotePersonRepository->findAdditionalData($fullname);
            // * Result return with Person + additionnal data.
            return new Result(
                200,
                $person->copyWith(
                    portrait: $additionnalData->portrait,
                    description: $additionnalData->description,
                )
            );
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
