<?php

namespace Src\MedicalReference\Dto;

use Carbon\Carbon;
use Src\MedicalReference\Dto\Common\OID;

class BaseDto
{
    /**
     * @var OID
     */
    private $id;
    /**
     * @var Carbon
     */
    private $effectiveTime;
    /**
     * @var string
     */
    private $confidentialityCode;
    /**
     * @var OID
     */
    private $setId;
    /**
     * @var string
     */
    private $versionNumber;

    /**
     * @param OID $id
     * @param Carbon $effectiveTime
     * @param string $confidentialityCode
     * @param OID $setId
     * @param string $versionNumber
     */
    public function __construct(OID $id, Carbon $effectiveTime, string $confidentialityCode, OID $setId, string $versionNumber)
    {
        $this->id = $id;
        $this->effectiveTime = $effectiveTime;
        $this->confidentialityCode = $confidentialityCode;
        $this->setId = $setId;
        $this->versionNumber = $versionNumber;
    }

    public function getId(): OID
    {
        return $this->id;
    }

    public function getEffectiveTime(): Carbon
    {
        return $this->effectiveTime;
    }

    public function getConfidentialityCode(): string
    {
        return $this->confidentialityCode;
    }

    public function getSetId(): OID
    {
        return $this->setId;
    }

    public function getVersionNumber(): string
    {
        return $this->versionNumber;
    }
}
