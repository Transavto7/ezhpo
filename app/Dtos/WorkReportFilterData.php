<?php

namespace App\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class WorkReportFilterData extends DataTransferObject
{
    /**
     * @var string|null
     */
    public ?string $dateFrom = null;
    /**
     * @var string|null
     */
    public ?string $dateTo = null;
    /**
     * @var int|null|string
     */
    public ?int $userId = null;
    /**
     * @var int|null|string
     */
    public ?int $pvId = null;
    /**
     * @var int|string
     */
    public int $perPage = 150;
}