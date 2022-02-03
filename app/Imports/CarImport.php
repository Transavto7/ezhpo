<?php

namespace App\Imports;

use App\Car;
use App\Company;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class CarImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function generateHash () {
        $hash = rand(500000, 799000);
        $findElem = Car::where('hash_id', $hash)->first();

        if($findElem) {
            return $this->generateHash();
        }

        return $hash;
    }

    public function model(array $row)
    {
        $company_id = $row[2];
        $company_id_save = $company_id;

        $company_id = Company::where('hash_id', $company_id)->first();
        $date = date('d.m.Y H:i:s');

        if($row[0] == 'gos_number' || $row[1] == 'mark_model') return;

        $hash_id = $this->generateHash();

        if(!empty($row[3])) {
            $dublicate_old_id = Car::where('old_id', $row[3])->first();
        } else {
            $dublicate_old_id = false;
        }

        if($company_id && !$dublicate_old_id) {
            $data = [
                'hash_id' => $hash_id,
                'gos_number' => $row[0] ?? rand(500000, 799000),
                'mark_model' => $row[1] ?? '',
                'company_id' => $company_id->id
            ];

            if(!empty($row[3]) && $row[3] > 0) {
                $data['old_id'] = $row[3];
            }   

            return new Car($data);
        } else {
            Log::channel('import')->info("[IMPORT - АВТОМОБИЛЬ][$date] - НЕ найдена Компания с id='$company_id_save'");
        }
    }
}
