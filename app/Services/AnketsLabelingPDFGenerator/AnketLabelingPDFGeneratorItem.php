<?php

namespace App\Services\AnketsLabelingPDFGenerator;

use App\Enums\AnketLabelingType;

final class AnketLabelingPDFGeneratorItem
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
     * @var AnketLabelingType
     */
    private $anketType;

    /**
     * @param string $qrCode
     * @param int $id
     * @param AnketLabelingType $anketType
     */
    public function __construct(string $qrCode, int $id, AnketLabelingType $anketType)
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

    public function getAnketType(): AnketLabelingType
    {
        return $this->anketType;
    }
}
