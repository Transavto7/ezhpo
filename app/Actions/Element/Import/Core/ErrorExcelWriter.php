<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Core;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ErrorExcelWriter
{
    /** @var string  */
    protected $filePrefix = 'Ошибки_импорта_';

    /** @var string  */
    protected $disk = 'export';

    protected $headers = [];

    /** @var Spreadsheet */
    protected $spreadsheet;

    public function __construct(string $templatePath)
    {
        $this->spreadsheet = IOFactory::load($templatePath);
    }

    /**
     * @param ErrorObject[] $errors
     * @return string
     * @throws Exception
     */
    public function writeErrors(array $errors): string
    {
        return $this
            ->writeBody($errors)
            ->saveFile();
    }

    /**
     * @param string $disk
     * @return self
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    protected function writeBody(array $errors): self
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray(array_map(function (ErrorObject $item) {
            return $item->toArray();
        }, $errors), null, 'A2');

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function saveFile(): string
    {
        $fileName = sprintf('%s_%s.xlsx', $this->filePrefix, Carbon::now()->format('d.m.Y_H.i'));
        $filePath = Storage::disk($this->disk)->path($fileName);
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($filePath);

        return $fileName;
    }
}
