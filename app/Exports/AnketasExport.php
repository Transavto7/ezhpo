<?php

namespace App\Exports;

use App\Enums\FormTypeEnum;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Throwable;

class AnketasExport implements FromView, WithBatchInserts, WithChunkReading
{
    private $forms;
    private $fields;
    private $exportPrikaz;

    public function __construct($forms, $fields, $exportPrikaz = false)
    {
        $this->forms = $forms;
        $this->fields = $fields;
        $this->exportPrikaz = $exportPrikaz;
    }

    public function view(): View
    {
        try {
            $fields = [];

            $firstForm = $this->forms[0]['type_anketa'];

            if (in_array($firstForm, [FormTypeEnum::TECH, FormTypeEnum::BDD, FormTypeEnum::PRINT_PL])) {
                foreach ($this->fields as $key => $value) {
                    if ($key === "driver_id") {
                        $fields['driver_id'] = "ID водителя";
                        $fields['driver_fio'] = "ФИО водителя";
                    } elseif ($key === "car_id") {
                        $fields['car_gos_number'] = $value;
                        $fields['car_id'] = "ID автомобиля";
                    } elseif ($key === "company_id") {
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    } else {
                        $fields[$key] = $value;
                    }
                }
            }

            if ($firstForm === FormTypeEnum::MEDIC) {
                if (!$this->exportPrikaz) {
                    $fields['id'] = "ID записи";
                }

                foreach ($this->fields as $key => $value) {
                    if ($key === "driver_id") {
                        $fields['driver_id'] = "ID водителя";
                        $fields['driver_fio'] = "ФИО водителя";
                    } elseif ($key === "car_id") {
                        $fields['car_gos_number'] = $value;
                    } elseif ($key === "company_id") {
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    } else {
                        $fields[$key] = $value;
                    }
                }
            } elseif ($firstForm === FormTypeEnum::TECH) {
                if (!$this->exportPrikaz) {
                    $fields['id'] = "ID записи";
                }
            } else {
                $fields['id'] = "ID записи";
                foreach ($this->fields as $key => $value) {
                    if ($key === "driver_id") {
                        $fields['driver_id'] = $value;
                        $fields['driver_fio'] = "ФИО водителя";
                    } elseif ($key === "car_id") {
                        $fields['car_gos_number'] = $value;
                    } elseif ($key === "company_id") {
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    } else {
                        $fields[$key] = $value;
                    }
                }
            }
        } catch (Throwable $th) {
            $fields = $this->fields;
        }

        return view('home-export', [
            'data' => $this->forms,
            'fields' => $fields
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
