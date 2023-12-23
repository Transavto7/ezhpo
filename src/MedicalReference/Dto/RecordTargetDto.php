<?php

namespace Src\MedicalReference\Dto;

use Src\MedicalReference\Dto\Common\AddressDto;
use Src\MedicalReference\Dto\Common\OID;
use Src\MedicalReference\Dto\Common\OrganizationDto;
use Src\MedicalReference\Dto\Common\PatientDto;

class RecordTargetDto
{
    /**
     * @var OID
     */
    private $id;
    /**
     * @var OID
     */
    private $idSnils;
    /**
     * @var AddressDto
     */
    private $addrResidential;
    /**
     * @var PatientDto
     */
    private $patient;
    /**
     * @var OrganizationDto
     */
    private $providerOrganization;

    /**
     * @param OID $id
     * @param OID $idSnils
     * @param AddressDto $addrResidential
     * @param PatientDto $patient
     * @param OrganizationDto $providerOrganization
     */
    public function __construct(OID $id, OID $idSnils, AddressDto $addrResidential, PatientDto $patient, OrganizationDto $providerOrganization)
    {
        $this->id = $id;
        $this->idSnils = $idSnils;
        $this->addrResidential = $addrResidential;
        $this->patient = $patient;
        $this->providerOrganization = $providerOrganization;
    }

    public function getId(): OID
    {
        return $this->id;
    }

    public function getIdSnils(): OID
    {
        return $this->idSnils;
    }

    public function getAddrResidential(): AddressDto
    {
        return $this->addrResidential;
    }

    public function getPatient(): PatientDto
    {
        return $this->patient;
    }

    public function getProviderOrganization(): OrganizationDto
    {
        return $this->providerOrganization;
    }
}
