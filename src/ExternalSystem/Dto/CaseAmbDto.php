<?php

namespace Src\ExternalSystem\Dto;

use Carbon\Carbon;
use Src\ExternalSystem\Dto\Common\DoctorDto ;
use Src\ExternalSystem\Dto\Common\MedRecordDto;
use Src\ExternalSystem\Dto\Common\StepDto;

final class CaseAmbDto
{
    /**
     * @var Carbon
     */
    private $openDate;
    /**
     * @var Carbon
     */
    private $closeDate;
    /**
     * @var string
     */
    private $historyNumber;
    /**
     * @var string
     */
    private $idCaseMis;
    /**
     * @var int|null
     */
    private $idCaseAidType;
    /**
     * @var int
     */
    private $idPaymentType;
    /**
     * @var int
     */
    private $confidentiality;
    /**
     * @var int
     */
    private $doctorConfidentiality;
    /**
     * @var int
     */
    private $curatorConfidentiality;
    /**
     * @var string
     */
    private $idLpu;
    /**
     * @var int
     */
    private $idCaseResult;
    /**
     * @var string
     */
    private $comment;
    /**
     * @var string
     */
    private $idPatientMis;
    /**
     * @var int|null
     */
    private $admissionCondition;
    /**
     * @var int
     */
    private $caseVisitType;
    /**
     * @var int|null
     */
    private $idCasePurpose;
    /**
     * @var int
     */
    private $idCaseType;
    /**
     * @var int|null
     */
    private $idAmbResult;
    /**
     * @var bool|null
     */
    private $isActive;
    /**
     * @var DoctorDto
     */
    private $doctorInCharge;
    /**
     * @var DoctorDto
     */
    private $authenticator;
    /**
     * @var DoctorDto
     */
    private $author;
    /**
     * @var array<StepDto>
     */
    private $steps;
    /**
     * @var array<MedRecordDto>
     */
    private $medRecords;

    //    private $Author xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto"
    //    private $LegalAuthenticator xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto"
    //    private $Guardian xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto" i:nil="true" /
    //    private $Steps xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto.Step"
    //    private $MedRecords xmlns:d5p1="http://schemas.datacontract.org/2004/07/N3.EMK.Dto.MedRec"
    /**
     * @param Carbon $openDate
     * @param Carbon $closeDate
     * @param string $historyNumber
     * @param string $idCaseMis
     * @param int|null $idCaseAidType
     * @param int $idPaymentType
     * @param int $confidentiality
     * @param int $doctorConfidentiality
     * @param int $curatorConfidentiality
     * @param string $idLpu
     * @param int $idCaseResult
     * @param string $comment
     * @param string $idPatientMis
     * @param int|null $admissionCondition
     * @param int $caseVisitType
     * @param int|null $idCasePurpose
     * @param int $idCaseType
     * @param int|null $idAmbResult
     * @param bool|null $isActive
     * @param DoctorDto $doctorInCharge
     * @param DoctorDto $authenticator
     * @param DoctorDto $author
     * @param StepDto[] $steps
     * @param MedRecordDto[] $medRecords
     */
    public function __construct(Carbon $openDate, Carbon $closeDate, string $historyNumber, string $idCaseMis, ?int $idCaseAidType, int $idPaymentType, int $confidentiality, int $doctorConfidentiality, int $curatorConfidentiality, string $idLpu, int $idCaseResult, string $comment, string $idPatientMis, ?int $admissionCondition, int $caseVisitType, ?int $idCasePurpose, int $idCaseType, ?int $idAmbResult, ?bool $isActive, DoctorDto $doctorInCharge, DoctorDto $authenticator, DoctorDto $author, array $steps, array $medRecords)
    {
        $this->openDate = $openDate;
        $this->closeDate = $closeDate;
        $this->historyNumber = $historyNumber;
        $this->idCaseMis = $idCaseMis;
        $this->idCaseAidType = $idCaseAidType;
        $this->idPaymentType = $idPaymentType;
        $this->confidentiality = $confidentiality;
        $this->doctorConfidentiality = $doctorConfidentiality;
        $this->curatorConfidentiality = $curatorConfidentiality;
        $this->idLpu = $idLpu;
        $this->idCaseResult = $idCaseResult;
        $this->comment = $comment;
        $this->idPatientMis = $idPatientMis;
        $this->admissionCondition = $admissionCondition;
        $this->caseVisitType = $caseVisitType;
        $this->idCasePurpose = $idCasePurpose;
        $this->idCaseType = $idCaseType;
        $this->idAmbResult = $idAmbResult;
        $this->isActive = $isActive;
        $this->doctorInCharge = $doctorInCharge;
        $this->authenticator = $authenticator;
        $this->author = $author;
        $this->steps = $steps;
        $this->medRecords = $medRecords;
    }

    public function getOpenDate(): Carbon
    {
        return $this->openDate;
    }

    public function getCloseDate(): Carbon
    {
        return $this->closeDate;
    }

    public function getHistoryNumber(): string
    {
        return $this->historyNumber;
    }

    public function getIdCaseMis(): string
    {
        return $this->idCaseMis;
    }

    public function getIdCaseAidType(): ?int
    {
        return $this->idCaseAidType;
    }

    public function getIdPaymentType(): int
    {
        return $this->idPaymentType;
    }

    public function getConfidentiality(): int
    {
        return $this->confidentiality;
    }

    public function getDoctorConfidentiality(): int
    {
        return $this->doctorConfidentiality;
    }

    public function getCuratorConfidentiality(): int
    {
        return $this->curatorConfidentiality;
    }

    public function getIdLpu(): string
    {
        return $this->idLpu;
    }

    public function getIdCaseResult(): int
    {
        return $this->idCaseResult;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getIdPatientMis(): string
    {
        return $this->idPatientMis;
    }

    public function getAdmissionCondition(): ?int
    {
        return $this->admissionCondition;
    }

    public function getCaseVisitType(): int
    {
        return $this->caseVisitType;
    }

    public function getIdCasePurpose(): ?int
    {
        return $this->idCasePurpose;
    }

    public function getIdCaseType(): int
    {
        return $this->idCaseType;
    }

    public function getIdAmbResult(): ?int
    {
        return $this->idAmbResult;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function getDoctorInCharge(): DoctorDto
    {
        return $this->doctorInCharge;
    }

    public function getAuthenticator(): DoctorDto
    {
        return $this->authenticator;
    }

    public function getAuthor(): DoctorDto
    {
        return $this->author;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getMedRecords(): array
    {
        return $this->medRecords;
    }


}
