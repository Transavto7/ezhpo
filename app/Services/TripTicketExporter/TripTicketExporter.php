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
     * @param EntityId[] $ids
     * @return Xlsx
     * @throws Exception
     */
    public function massExport(array $ids): Xlsx
    {
        return $this->generator->generate($ids);
    }
}
