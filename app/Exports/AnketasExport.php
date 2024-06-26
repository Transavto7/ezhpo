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
        $fields = null;
        // Определить тип анкет

        try {
            $fields = [];
            if($this->anketas[0]['type_anketa'] === "medic"){
                if(!isset($_GET['exportPrikaz'])){
                    $fields['id'] = "ID записи";
                }

                foreach ($this->fields as $key => $value) {
                    if($key === "driver_id"){
                        $fields['driver_id'] = "ID водителя";
                        $fields['driver_fio'] = "ФИО водителя";
                    }elseif ($key === "car_id"){
                        $fields['car_gos_number'] = $value;
                    }elseif ($key === "company_id"){
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    }else{
                        $fields[$key] = $value;
                    }
                }
            } elseif ($this->anketas[0]['type_anketa'] === "tech"){
                if(!isset($_GET['exportPrikaz'])){
                    $fields['id'] = "ID записи";
                }

                foreach ($this->fields as $key => $value) {
                    if ($key === "driver_id"){
                        $fields['driver_id'] = "ID водителя";
                        $fields['driver_fio'] = "ФИО водителя";
                    } elseif ($key === "car_id"){
                        $fields['car_gos_number'] = $value;
                        $fields['car_id'] = "ID автомобиля";
                    } elseif ($key === "company_id"){
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    } else {
                        $fields[$key] = $value;
                    }
                }
            }elseif ($this->anketas[0]['type_anketa'] === "bdd") {
                $fields['id'] = "ID записи";
                foreach ($this->fields as $key => $value) {
                    if($key === "driver_id"){
                        $fields['driver_id'] = "ID водителя";
                        $fields['driver_fio'] = "ФИО водителя";
                    }elseif ($key === "car_id"){
                        $fields['car_gos_number'] = $value;
                        $fields['car_id'] = "ID автомобиля";
                    }elseif ($key === "company_id"){
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    }else{
                        $fields[$key] = $value;
                    }
                }
            }elseif ($this->anketas[0]['type_anketa'] === "pechat_pl"){
                $fields['id'] = "ID записи";
                foreach ($this->fields as $key => $value) {
                    if($key === "driver_id"){
                        $fields['driver_id'] = "ID водителя";
                        $fields['driver_fio'] = "ФИО водителя";
                    }elseif ($key === "car_id"){
                        $fields['car_gos_number'] = $value;
                        $fields['car_id'] = "ID автомобиля";
                    }elseif ($key === "company_id"){
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    }else{
                        $fields[$key] = $value;
                    }
                }
            }else{
                $fields['id'] = "ID записи";
                foreach ($this->fields as $key => $value) {
                    if($key === "driver_id"){
                        $fields['driver_id'] = $value;
                        $fields['driver_fio'] = "ФИО водителя";
                    }elseif ($key === "car_id"){
                        $fields['car_gos_number'] = $value;
                    }elseif ($key === "company_id"){
                        $fields['company_name'] = $value;
                        $fields['company_id'] = "ID компании";
                    }else{
                        $fields[$key] = $value;
                    }
                }
            }

            return view('home-export', [
                'data' => $this->anketas,
                'fields' => $fields,
            ]);
        } catch (\Throwable $th) {
            return view('home-export', [
                'data' => $this->anketas,
                'fields' => $this->fields,
            ]);
        }
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
