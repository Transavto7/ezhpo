<?php

namespace App\Services\FindSimilarElement\FindSimilarCar;

use App\Exceptions\CarWithSameGosNumberAlreadyExist;
use App\Exceptions\CarWithSameGosNumberAlreadyExistInTrash;
use App\Exceptions\CarWithSameVinAlreadyExist;
use App\Exceptions\CarWithSameVinAlreadyExistInTrash;
use App\Services\FindSimilarElement\Repositories\CarRepository;

final class FindSimilarCarHandler
{
    /**
     * @var CarRepository
     */
    private $repository;

    public function __construct(CarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(FindSimilarCarAction $action)
    {
        $car = $this->repository->findByGosNumber(
            $action->getGosNumber(),
            $action->getCompany()->id,
            $action->getExcludeId(),
            $action->withTrashed()
        );

        if ($car && ! $car->trashed()) {
            throw new CarWithSameGosNumberAlreadyExist();
        }
        if ($car && $car->trashed()) {
            throw new CarWithSameGosNumberAlreadyExistInTrash();
        }

        if (! $action->getVin()) {
            return;
        }

        $car = $this->repository->findByVin(
            $action->getVin(),
            $action->getCompany()->id,
            $action->getExcludeId(),
            $action->withTrashed()
        );

        if ($car && ! $car->trashed()) {
            throw new CarWithSameVinAlreadyExist();
        }
        if ($car && $car->trashed()) {
            throw new CarWithSameVinAlreadyExistInTrash();
        }
    }
}
