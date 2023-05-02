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
        //array_unshift($this->fields, $this->fields['driver_fio'] = "ФИО водителя");
        $fields = [];

        foreach ($this->fields as $key => $value) {
            if($key === "driver_id"){
                $fields['driver_fio'] = $value;
            }else{
                $fields[$key] = $value;
            }   
        }

        return view('home-export', [
            'data' => $this->anketas,
            'fields' => $fields,
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
