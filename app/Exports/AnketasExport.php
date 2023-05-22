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
        $fields = null;

        // Определить тип анкет
        //dd($this->anketas[0]['type_anketa']);

        if(is_array($this->anketas)){
            $fields = [];
            if($this->anketas[0]['type_anketa'] === "medic"){
                $fields['id'] = "ID записи";
    
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
            }elseif ($this->anketas[0]['type_anketa'] === "bdd"){
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
        }
        

        if(!is_array($this->anketas)){
            return view('home-export', [
                'data' => $this->anketas,
                'fields' => $this->fields,
            ]);  
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
