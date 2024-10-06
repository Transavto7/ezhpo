<?php

namespace App\Actions\Reports\OneC\Create;

use App\Enums\ReportStatus;
use App\Enums\ReportType;

class ReportAction
{
    /**
     * @var ReportType
     */
    private $type;

    /**
     * @var ReportStatus
     */
    private $status;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var ReportPayload
     */
    private $payload;

    /**
     * @param ReportType $type
     * @param ReportStatus $status
     * @param string $userId
     * @param ReportPayload $payload
     */
    public function __construct(ReportType $type, ReportStatus $status, string $userId, ReportPayload $payload)
    {
        $this->type = $type;
        $this->status = $status;
        $this->userId = $userId;
        $this->payload = $payload;
    }

    public function getType(): ReportType
    {
        return $this->type;
    }

    public function getStatus(): ReportStatus
    {
        return $this->status;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getPayload(): ReportPayload
    {
        return $this->payload;
    }
}
