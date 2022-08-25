<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AnketasExport implements FromView, WithBatchInserts, WithChunkReading
{
    private $anketas;
    private $fields;

    public function __construct($anketas, $fields)
    {
        $this->anketas = $anketas;
        $this->fields = $fields;
    }

    public function view(): View
    {
        return view('home-export', [
            'data' => $this->anketas,
            'fields' => $this->fields,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
