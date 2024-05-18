<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Drivers;

use App\Actions\Element\Export\Core\ExcelWriter;
use App\Actions\Element\Export\ExportElementHandler;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

final class ExportDriversHandler implements ExportElementHandler
{
    /** @var ExcelWriter */
    private $writer;

    /** @var ExportDriverRepository */
    private $repository;

    private $tableHeaders= [
        'Название компании',
        'ФИО',
        'ID работника',
    ];

    public function __construct()
    {
        $this->writer = new ExcelWriter();
        $this->repository = new ExportDriverRepository();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function handle(): string
    {
        $elements = $this->repository->getExportDrivers();

        return $this->writer
            ->setHeaders($this->tableHeaders)
            ->setDisk('export')
            ->setPrefix('Водители_')
            ->write($elements);
    }

    public static function create(): ExportElementHandler
    {
        return new self();
    }
}
