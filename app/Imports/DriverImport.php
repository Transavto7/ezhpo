<?php

namespace App\Imports;

use App\Company;
use App\Driver;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class DriverImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function generateHash () {
        $hash = rand(100000, 499000);
        $findElem = Driver::where('hash_id', $hash)->first();

        if($findElem) {
            return $this->generateHash();
        }

        return $hash;
    }

    public function model(array $row)
    {
        $company_id = $row[4];
        $company_id_save = $company_id;

        $company_id = Company::where('hash_id', $company_id)->first();
        $date = date('d.m.Y H:i:s');

        if($row[0] == 'fio' || $row[1] == 'year_birthday') return;

        $hash_id = $this->generateHash();

        if(!empty($row[9])) {
            $dublicate_old_id = Driver::where('old_id', $row[9])->first();
        } else {
            $dublicate_old_id = false;
        }

        if($company_id && !$dublicate_old_id) {
            $data = [
                'hash_id' => $hash_id,
                'fio' => $row[0] ?? '-',
                'year_birthday' => $row[1] ?? date('Y-m-d'),
                'gender' => $row[2] ?? '',
                'group_risk' => $row[3] ?? '',
                'company_id' => $company_id->id,
                'payment_form' => $row[5] ?? '',
                'count_pl' => $row[6] ?? '',
                'note' => $row[7] ?? '',
                'procedure_pv' => $row[8] ?? '',
                'phone' => $row[10] ?? ''
            ];

            if(!empty($row[9]) && $row[9] > 0) {
                $data['old_id'] = $row[9];
            }

            return new Driver($data);
        } else {
            Log::channel('import')->info("[IMPORT - ВОДИТЕЛИ][$date] - НЕ найдена Компания с id='$company_id_save'");
        }
    }
}
