<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Core;

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

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->setUp();
    }

    /**
     * @return Generator<array>
     */
    public function rows(): Generator
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $range = $this->getParseRange($sheet);

        foreach ($sheet->rangeToArray($range) as $row) {
            if ($row[1] === null) {
                continue;
            }

            yield $row;
        }
    }

    private function setUp()
    {
        $this->spreadsheet = IOFactory::load($this->filePath);
    }

    private function getParseRange(Worksheet $sheet): string
    {
        return sprintf('A2:%s%d', $sheet->getHighestColumn(), $sheet->getHighestRow());
    }
}
