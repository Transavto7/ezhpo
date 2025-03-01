<?php

namespace App\Services\DuplicateChecker;

use App\Services\DuplicateChecker\Dto\Inspection;

interface DuplicateCheckerInterface
{
    public function check(Inspection $inspection): bool;
}
