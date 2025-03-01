<?php

namespace App\Services\DuplicateChecker\Repositories;

use App\Services\DuplicateChecker\Dto\Inspection;

interface DuplicateRepository
{
    public function getDuplicates(Inspection $inspection);
}
