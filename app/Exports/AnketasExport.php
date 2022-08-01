<?php

namespace App\Exports;

use App\Anketa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AnketasExport implements FromView
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
}
