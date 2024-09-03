<?php

namespace App\Services\DuplicateChecker;

use App\Services\DuplicateChecker\Dto\Inspection;
use App\Services\DuplicateChecker\Repositories\DuplicateRepository;

class DuplicateMedicalInspectionChecker implements DuplicateCheckerInterface
{
    /**
     * @var DuplicateRepository
     */
    private $repository;

    /**
     * @param DuplicateRepository $repository
     */
    public function __construct(DuplicateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function check(Inspection $inspection): bool
    {
        $duplicates = $this->repository->getDuplicates($inspection);

        if (count($duplicates)) {
            return true;
        }

        return false;
    }
}
