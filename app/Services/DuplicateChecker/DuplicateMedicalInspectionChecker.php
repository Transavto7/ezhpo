<?php

namespace App\Services\DuplicateChecker;

use App\Services\DuplicateChecker\Dto\Inspection;
use App\Services\DuplicateChecker\Repositories\MedicalInspectionDuplicatesRepository;

class DuplicateMedicalInspectionChecker implements DuplicateCheckerInterface
{
    /**
     * @var MedicalInspectionDuplicatesRepository
     */
    private $repository;

    /**
     * @param MedicalInspectionDuplicatesRepository $repository
     */
    public function __construct(MedicalInspectionDuplicatesRepository $repository)
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
