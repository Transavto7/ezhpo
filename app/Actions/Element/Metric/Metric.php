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
    protected $import;

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
        $this->import = $data['import'];
        $this->docRequest = $data['docRequest'];
    }

    public function getName(): string
    {
        return $this->name ?: 'Компания не указана';
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
    public function getImport()
    {
        return $this->import;
    }

    /**
     * @return int|mixed
     */
    public function getDocRequest()
    {
        return $this->docRequest;
    }
}
