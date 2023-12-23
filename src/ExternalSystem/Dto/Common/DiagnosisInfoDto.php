<?php

namespace Src\ExternalSystem\Dto\Common;

use Carbon\Carbon;

final class DiagnosisInfoDto
{
    /**
     * @var Carbon
     */
    private $diagnosisDate;
    /**
     * @var int
     */
    private $idDiagnosisType;
    /**
     * @var string
     */
    private $comment;
    /**
     * @var string
     */
    private $mkbCode;

    /**
     * @param Carbon $diagnosisDate
     * @param int $idDiagnosisType
     * @param string $comment
     * @param string $mkbCode
     */
    public function __construct(Carbon $diagnosisDate, int $idDiagnosisType, string $comment, string $mkbCode)
    {
        $this->diagnosisDate = $diagnosisDate;
        $this->idDiagnosisType = $idDiagnosisType;
        $this->comment = $comment;
        $this->mkbCode = $mkbCode;
    }

    public function getDiagnosisDate(): Carbon
    {
        return $this->diagnosisDate;
    }

    public function getIdDiagnosisType(): int
    {
        return $this->idDiagnosisType;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getMkbCode(): string
    {
        return $this->mkbCode;
    }
}
