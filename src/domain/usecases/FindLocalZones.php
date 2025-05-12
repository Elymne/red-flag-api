<?php

declare(strict_types=1);

namespace Domain\Usecases;

use Core\Result;
use Core\Usecase;
use Domain\Repositories\LocalZoneRepository;
use Throwable;

class FindLocalZones extends Usecase
{
    private LocalZoneRepository $_localZoneRepository;

    public function __construct(LocalZoneRepository $localZoneRepository)
    {
        $this->_localZoneRepository = $localZoneRepository;
    }

    /**
     * @param FindZonesParams $params
     */
    public function perform(mixed $params): Result
    {
        try {
            // Check $params type.
            if (!isset($params) || !($params instanceof FindZonesParams)) {
                return new Result(code: 400, data: "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
            }
            // Search remote zone.
            $zones = $this->_localZoneRepository->findMany(name: $params->name);
            // Return zones from remotes.
            return new Result(200, $zones);
        } catch (Throwable $err) {
            return new Result(code: 500, data: "Action failure : Internal Server Error.");
        }
    }
}
