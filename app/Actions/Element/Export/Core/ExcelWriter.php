<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Core;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class ExcelWriter
{
    /** @var string  */
    protected $filePrefix = 'Экспорт_';

    /** @var string  */
    protected $disk = 'export';

    /** @var string[]  */
    protected $headers = [];

    /** @var Spreadsheet */
    private $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    /**
     * @param ElementObject[] $elements
     * @return string
     * @throws Exception
     */
    public function write(array $elements): string
    {
        return $this
            ->writeHeaders()
            ->writeBody($elements)
            ->saveFile();
    }

    /**
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
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

    public function setPrefix(string $prefix): self
    {
        $this->filePrefix = $prefix;
        return $this;
    }

    protected function writeHeaders(): self
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray($this->headers);

        return $this;
    }

    protected function writeBody(array $errors): self
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->fromArray(array_map(function (ElementObject $item) {
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
