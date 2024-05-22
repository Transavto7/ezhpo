<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars;

use App\Actions\Element\Import\Cars\ImportObjects\ErrorCar;
use App\Actions\Element\Import\Core\ErrorExcelWriter;
use App\Actions\Element\Import\Core\ExcelReader;
use App\Actions\Element\Import\Drivers\ImportObjects\ErrorDriver;
use App\Actions\Element\Import\Exceptions\FoundedNotValidElements;
use App\Actions\Element\Import\ImportElementAction;
use App\Actions\Element\Import\ImportElementHandler;
use App\Actions\Element\Import\ImportElementResponse;
use Exception;
use Illuminate\Support\Facades\Storage;

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

    public function __construct()
    {
        $this->hydrator = new CarHydrator();
        $this->recordHandler = new CarRecordHandler();
        $this->validator = new CarValidator();
        $this->errorWriter = new ErrorExcelWriter(Storage::disk('examples')->path('cars_example.xlsx'));
    }

    /**
     * @param ImportElementAction $action
     * @return void
     * @throws FoundedNotValidElements
     * @throws Exception
     */
    public function handle(ImportElementAction $action): ImportElementResponse
    {
        $reader = new ExcelReader($action->getFilePath());
        /** @var ErrorDriver[] $errors */
        $errors = [];
        $rowsCounter = 0;
        $acceptedRowsCounter = 0;

        foreach ($reader->rows() as $row) {
            $rowsCounter++;
            $associatedRow = $this->hydrator->associate($row);
            $this->validator->validate($associatedRow);
            if ($this->validator->hasErrors()) {
                $errors[] = new ErrorCar(
                    $associatedRow['companyName'],
                    $associatedRow['companyInn'],
                    $associatedRow['number'],
                    $associatedRow['markAndModel'],
                    $associatedRow['category'],
                    $associatedRow['trailer'],
                    $associatedRow['dateTechView'],
                    $associatedRow['dateOsago'],
                    $associatedRow['dateSkzi'],
                    implode(' ', $this->validator->errors())
                );

                continue;
            }

            $importedDriver = $this->hydrator->hydrate($associatedRow);
            $result = $this->recordHandler->handle($importedDriver);
            if ($result) {
                $acceptedRowsCounter++;
            }
        }

        if ($this->recordHandler->hasErrors()) {
            $errors = array_merge($errors, $this->recordHandler->errors());
        }

        if (count($errors) !== 0) {
            $filePath = $this->errorWriter
                ->setDisk($this->errorFileDisk)
                ->writeErrors($errors);

            return new ImportElementResponse(
                $rowsCounter,
                $acceptedRowsCounter,
                count($errors),
                Storage::disk($this->errorFileDisk)->url($filePath)
            );
        }

        return new ImportElementResponse($rowsCounter, $acceptedRowsCounter);
    }

    /**
     * @return ImportElementHandler
     */
    public static function create(): ImportElementHandler
    {
        return new self();
    }
}
