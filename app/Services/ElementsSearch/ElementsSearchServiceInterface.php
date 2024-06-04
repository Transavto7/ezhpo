<?php

namespace App\Services\ElementsSearch;

use App\Dto\ElementDto;

interface ElementsSearchServiceInterface
{
    /**
     * @param string $identifier
     * @return array<ElementDto>
     */
    public function search(string $identifier): array;
}
