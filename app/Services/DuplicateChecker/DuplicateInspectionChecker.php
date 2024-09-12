<?php

namespace App\Services\DuplicateChecker;

use App\Services\DuplicateChecker\Dto\Inspection;
use App\Services\DuplicateChecker\Repositories\InspectionDuplicatesRepository;

class DuplicateInspectionChecker implements DuplicateCheckerInterface
{
    /**
     * @var InspectionDuplicatesRepository
     */
    private $repository;

    /**
     * @param InspectionDuplicatesRepository $repository
     */
    public function __construct(InspectionDuplicatesRepository $repository)
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
