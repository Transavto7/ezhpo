<?php
declare(strict_types=1);

namespace App\Actions\Drivers\Import\Reader;

use App\Actions\Drivers\Import\ImportObjects\ErrorDriver;
use App\Actions\Drivers\Import\ImportObjects\ImportedDriver;
use Exception;
use Generator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class ExcelReader
{
    /** @var string */
    private $filePath;

    /** @var Spreadsheet */
    private $spreadsheet;

    /** @var Hydrator */
    private $hydrator;

    /** @var Validator */
    private $validator;

    /** @var ErrorDriver[]  */
    private $errorDrivers = [];

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->hydrator = new Hydrator();
        $this->validator = new Validator();
        $this->setUp();
    }

    /**
     * @return Generator<ImportedDriver>
     * @throws Exception
     */
    public function importingDrivers(): Generator
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $range = $this->getParseRange($sheet);

        foreach ($sheet->rangeToArray($range) as $row) {
            $associatedRow = $this->hydrator->associate($row);
            $this->validator->validate($associatedRow);
            if ($this->validator->hasErrors()) {
                $this->errorDrivers[] = new ErrorDriver(
                    $associatedRow['companyInn'],
                    $associatedRow['fullName'],
                    $associatedRow['birthday'],
                    $associatedRow['companyName'],
                    $associatedRow['gender'],
                    $associatedRow['phone'],
                    $associatedRow['snils'],
                    $associatedRow['license'],
                    $associatedRow['licenseIssuedAt'],
                    implode('.', $this->validator->errors())
                );

                continue;
            }

            yield $this->hydrator->hydrate($associatedRow);
        }
    }

    /**
     * @return ErrorDriver[]
     */
    public function getErrorDrivers(): array
    {
        return $this->errorDrivers;
    }

    private function setUp()
    {
        $this->spreadsheet = IOFactory::load($this->filePath);
    }

    private function getParseRange(Worksheet $sheet): string
    {
        return sprintf('A2:%s%n', $sheet->getHighestColumn(), $sheet->getHighestRow());
    }

    public function hasErrors(): bool
    {
        return count($this->errorDrivers) !== 0;
    }
}
