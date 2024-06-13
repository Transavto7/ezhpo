<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Cars;

use App\Actions\Element\Export\Core\ExcelWriter;
use App\Actions\Element\Export\ExportElementAction;
use App\Actions\Element\Export\ExportElementHandler;
use DomainException;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

final class ExportCarsHandler implements ExportElementHandler
{
    /** @var ExcelWriter */
    private $writer;

    /** @var ExportCarsRepository */
    private $repository;

    /** @var ExportElementAction */
    private $action;

    private $tableHeaders= [
        'Название компании',
        'Гос. номер',
        'Марка и модель',
        'Категория Т/С',
        'ID Т/С',
    ];

    public function __construct(ExportElementAction $action)
    {
        $this->writer = new ExcelWriter();
        $this->repository = new ExportCarsRepository();
        $this->action = $action;
    }

    /**
     * @return string
     * @throws DomainException
     * @throws Exception
     */
    public function handle(): string
    {
        $elements = $this->repository->getExportCars(
            $this->action->exportAll(),
            $this->action->getCompanyId()
        );

        return $this->writer
            ->setHeaders($this->tableHeaders)
            ->setDisk('export')
            ->setPrefix('ТС_')
            ->write($elements);
    }

    public static function create(ExportElementAction $action): ExportElementHandler
    {
        return new self($action);
    }
}
