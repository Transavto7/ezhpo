<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Drivers;

use App\Actions\Element\Export\Core\ExcelWriter;
use App\Actions\Element\Export\ExportElementAction;
use App\Actions\Element\Export\ExportElementHandler;
use DomainException;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

final class ExportDriversHandler implements ExportElementHandler
{
    /** @var ExcelWriter */
    private $writer;

    /** @var ExportDriverRepository */
    private $repository;

    /** @var ExportElementAction */
    private $action;

    private $tableHeaders= [
        'Название компании',
        'ФИО',
        'ID работника',
    ];

    public function __construct(ExportElementAction $action)
    {
        $this->writer = new ExcelWriter();
        $this->repository = new ExportDriverRepository();
        $this->action = $action;
    }

    /**
     * @return string
     * @throws DomainException
     * @throws Exception
     */
    public function handle(): string
    {
        $elements = $this->repository->getExportDrivers(
            $this->action->exportAll(),
            $this->action->getCompanyId()
        );

        return $this->writer
            ->setHeaders($this->tableHeaders)
            ->setDisk('export')
            ->setPrefix('Водители_')
            ->write($elements);
    }

    public static function create(ExportElementAction $action): ExportElementHandler
    {
        return new self($action);
    }
}
