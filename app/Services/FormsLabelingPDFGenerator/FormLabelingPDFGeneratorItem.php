<?php

namespace App\Services\FormsLabelingPDFGenerator;

use App\Enums\FormLabelingType;

final class FormLabelingPDFGeneratorItem
{
    /**
     * @var string
     */
    private $qrCode;
    /**
     * @var int
     */
    private $id;
    /**
     * @var FormLabelingType
     */
    private $anketType;

    /**
     * @param string $qrCode
     * @param int $id
     * @param FormLabelingType $anketType
     */
    public function __construct(string $qrCode, int $id, FormLabelingType $anketType)
    {
        $this->qrCode = $qrCode;
        $this->id = $id;
        $this->anketType = $anketType;
    }

    public function getQrCode(): string
    {
        return $this->qrCode;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAnketType(): FormLabelingType
    {
        return $this->anketType;
    }
}
