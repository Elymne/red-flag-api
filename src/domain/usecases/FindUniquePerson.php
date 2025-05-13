<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Models\PersonDetailed;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\RemotePersonRepository;
use Domain\Repositories\UuidRepository;
use Throwable;

class FindUniquePerson extends Usecase
{
    private UuidRepository $_uuidRepository;
    private LocalPersonRepository $_localPersonRepository;
    private RemotePersonRepository $_remotePersonRepository;

    public function __construct(UuidRepository $uuidRepository, LocalPersonRepository $localPersonRepository, RemotePersonRepository $remotePersonRepository)
    {
        $this->_uuidRepository = $uuidRepository;
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
            $uuidBytes = $this->_uuidRepository->toBytes($params->id);
            $person = $this->_localPersonRepository->findUnique($uuidBytes);
            // * Check the value.
            if (!isset($person)) {
                return new Result(code: 404, data: "Action failure : this person does not exists.");
            }
            // * Find additionnal data from remote person repository.
            $additionnalData = $this->_remotePersonRepository->findAdditionalData($person);
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
