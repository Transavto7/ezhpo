<?php

namespace App\Services\TripTicketExporter;

use App\ValueObjects\EntityId;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class TripTicketExporter
{
    /**
     * @var ExcelGenerator
     */
    private $generator;

    /**
     * @param ExcelGenerator $generator
     */
    public function __construct(ExcelGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param EntityId $id
     * @return Xlsx
     * @throws Exception
     */
    public function export(EntityId $id): Xlsx
    {
        return $this->generator->generate([$id]);
    }

    /**
     * @param EntityId $id
     * @return string
     * @throws Exception|\Exception
     */
    public function getExportFileName(EntityId $id): string
    {
        return $this->generator->generateFileName([$id]);
    }

    /**
     * @param EntityId[] $ids
     * @return string
     * @throws Exception|\Exception
     */
    public function getMassExportFileName(array $ids): string
    {
        return $this->generator->generateFileName($ids);
    }

    /**
     * @param EntityId[] $ids
     * @return Xlsx
     * @throws Exception
     */
    public function massExport(array $ids): Xlsx
    {
        return $this->generator->generate($ids);
    }
}
