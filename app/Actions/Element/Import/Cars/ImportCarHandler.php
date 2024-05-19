<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars;

use App\Actions\Element\Import\Cars\ImportObjects\ErrorCar;
use App\Actions\Element\Import\Core\ErrorExcelWriter;
use App\Actions\Element\Import\Core\ExcelReader;
use App\Actions\Element\Import\Drivers\DriverHydrator;
use App\Actions\Element\Import\Drivers\DriverRecordHandler;
use App\Actions\Element\Import\Drivers\DriverValidator;
use App\Actions\Element\Import\Drivers\ImportObjects\ErrorDriver;
use App\Actions\Element\Import\Exceptions\FoundedNotValidElements;
use App\Actions\Element\Import\ImportElementAction;
use App\Actions\Element\Import\ImportElementHandler;
use Exception;

final class ImportCarHandler implements ImportElementHandler
{
    /** @var CarHydrator */
    private $hydrator;

    /** @var CarValidator */
    private $validator;

    /** @var CarRecordHandler */
    private $recordHandler;

    /** @var ErrorExcelWriter */
    private $errorWriter;

    /** @var string  */
    private $errorFileDisk = 'export';

    /** @var string[] */
    private $errorColumns = [
        'Название компании',
        'ИНН компании',
        'Гос номер',
        'Марка и модель',
        'Категория ТС',
        'Прицеп (если есть)',
        'Дата ТО',
        'Дата ОСАГО',
        'Срок действия СКЗИ',
        'Ошибки',
    ];

    public function __construct()
    {
        $this->hydrator = new CarHydrator();
        $this->recordHandler = new CarRecordHandler();
        $this->validator = new CarValidator();
        $this->errorWriter = new ErrorExcelWriter();
    }

    /**
     * @param ImportElementAction $action
     * @return void
     * @throws FoundedNotValidElements
     * @throws Exception
     */
    public function handle(ImportElementAction $action): void
    {
        $reader = new ExcelReader($action->getFilePath());
        /** @var ErrorDriver[] $errors */
        $errors = [];

        foreach ($reader->rows() as $row) {
            $associatedRow = $this->hydrator->associate($row);
            $this->validator->validate($associatedRow);
            if ($this->validator->hasErrors()) {
                $errors[] = new ErrorCar(
                    $associatedRow['companyInn'],
                    $associatedRow['fullName'],
                    $associatedRow['birthday'],
                    $associatedRow['companyName'],
                    $associatedRow['gender'],
                    (string)$associatedRow['phone'],
                    $associatedRow['snils'],
                    $associatedRow['license'],
                    $associatedRow['licenseIssuedAt'],
                    implode('. ', $this->validator->errors())
                );

                continue;
            }

            $importedDriver = $this->hydrator->hydrate($associatedRow);
            $this->recordHandler->handle($importedDriver);
        }

        if ($this->recordHandler->hasErrors()) {
            $errors = array_merge($errors, $this->recordHandler->errors());
        }

        if (count($errors) !== 0) {
            $filePath = $this->errorWriter
                ->setHeaders($this->errorColumns)
                ->setDisk($this->errorFileDisk)
                ->writeErrors($errors);

            throw new FoundedNotValidElements($filePath, $this->errorFileDisk);
        }
    }

    /**
     * @return ImportElementHandler
     */
    public static function create(): ImportElementHandler
    {
        return new self();
    }
}
