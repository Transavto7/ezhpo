<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Drivers;

use App\Actions\Element\Import\Core\ErrorExcelWriter;
use App\Actions\Element\Import\Core\ExcelReader;
use App\Actions\Element\Import\Drivers\ImportObjects\ErrorDriver;
use App\Actions\Element\Import\Exceptions\FoundedNotValidElements;
use App\Actions\Element\Import\ImportElementAction;
use App\Actions\Element\Import\ImportElementHandler;
use Exception;

final class ImportDriverHandler implements ImportElementHandler
{
    /** @var DriverHydrator */
    private $hydrator;

    /** @var DriverValidator */
    private $validator;

    /** @var DriverRecordHandler */
    private $recordHandler;

    /** @var ErrorExcelWriter */
    private $errorWriter;

    /** @var string  */
    private $errorFileDisk = 'export';

    /** @var string[] */
    private $errorColumns = [
        'ИНН компании',
        'ФИО',
        'Дата рождения',
        'Название компании',
        'Пол',
        'Телефон',
        'СНИЛС',
        'Серия/номер ВУ',
        'Срок действия ВУ',
        'Ошибки',
    ];

    public function __construct()
    {
        $this->hydrator = new DriverHydrator();
        $this->recordHandler = new DriverRecordHandler();
        $this->validator = new DriverValidator();
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
                $errors[] = new ErrorDriver(
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
