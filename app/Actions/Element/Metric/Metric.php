<?php

namespace App\Actions\Element\Metric;

class Metric
{
    /** @var string|null */
    protected $name;

    /** @var int */
    protected $authorization;

    /** @var int */
    protected $logRequest;

    /** @var int */
    protected $reportRequest;

    /** @var int */
    protected $carImport;

    /** @var int */
    protected $driverImport;

    /** @var int */
    protected $addCarViaForm;

    /** @var int */
    protected $addDriverViaForm;

    /** @var int */
    protected $docRequest;

    /**
     * @param $name
     * @param array $data
     */
    public function __construct($name, array $data)
    {
        $this->name = $name;
        $this->authorization = $data['authorization'];
        $this->logRequest = $data['logRequest'];
        $this->reportRequest = $data['reportRequest'];
        $this->carImport = $data['carImport'];
        $this->driverImport = $data['driverImport'];
        $this->addCarViaForm = $data['addCarViaForm'];
        $this->addDriverViaForm = $data['addDriverViaForm'];
        $this->docRequest = $data['docRequest'];
    }

    public function getName(): string
    {
        return $this->name !== 'emptyName'
            ? $this->name
            : 'Компания не указана';
    }

    /**
     * @return int|mixed
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @return int|mixed
     */
    public function getLogRequest()
    {
        return $this->logRequest;
    }

    /**
     * @return int|mixed
     */
    public function getReportRequest()
    {
        return $this->reportRequest;
    }

    /**
     * @return int|mixed
     */
    public function getCarImport()
    {
        return $this->carImport;
    }

    /**
     * @return int|mixed
     */
    public function getDriverImport()
    {
        return $this->driverImport;
    }

    /**
     * @return int|mixed
     */
    public function getAddCarViaForm()
    {
        return $this->addCarViaForm;
    }

    /**
     * @return int|mixed
     */
    public function getAddDriverViaForm()
    {
        return $this->addDriverViaForm;
    }

    /**
     * @return int|mixed
     */
    public function getDocRequest()
    {
        return $this->docRequest;
    }
}
