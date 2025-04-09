<?php

namespace App\Services\FindSimilarElement\FindSimilarDriver;

use App\Exceptions\DriverWithSameNameAlreadyExist;
use App\Exceptions\DriverWithSameNameAlreadyExistInTrash;
use App\Services\FindSimilarElement\Repositories\DriverRepository;

final class FindSimilarDriverHandler
{
    /**
     * @var DriverRepository
     */
    private $repository;

    public function __construct(DriverRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(FindSimilarDriverAction $action)
    {
        $driver = $this->repository->findByName(
            $action->getName(),
            $action->getCompany()->id,
            $action->getExcludeId(),
            $action->withTrashed()
        );

        if ($driver && ! $driver->trashed()) {
            throw new DriverWithSameNameAlreadyExist();
        }
        if ($driver && $driver->trashed()) {
            throw new DriverWithSameNameAlreadyExistInTrash();
        }
    }
}
