<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Cars;

use App\Actions\Element\Export\Core\ExcelWriter;
use App\Actions\Element\Export\ExportElementHandler;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

final class ExportCarsHandler implements ExportElementHandler
{
    /** @var ExcelWriter */
    private $writer;

    /** @var ExportCarsRepository */
    private $repository;

    private $tableHeaders= [
        'Название компании',
        'Гос. номер',
        'Марка и модель',
        'Категория Т/С',
        'ID Т/С',
    ];

    public function __construct()
    {
        $this->writer = new ExcelWriter();
        $this->repository = new ExportCarsRepository();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function handle(): string
    {
        $elements = $this->repository->getExportCars();

        return $this->writer
            ->setHeaders($this->tableHeaders)
            ->setDisk('export')
            ->setPrefix('ТС_')
            ->write($elements);
    }

    public static function create(): ExportElementHandler
    {
        return new self();
    }
}
