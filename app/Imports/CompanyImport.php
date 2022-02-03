<?php

namespace App\Imports;

use App\Company;
use App\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;

class CompanyImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $company = Company::where('name', $row[0])->first();
        $date = date('d.m.Y H:i:s');
        $products_id = explode(',', $row[7]);
        $valid_products_id = [];

        foreach($products_id as $product_id) {
            if(Product::where('hash_id', $product_id)->first()) {
                array_push($valid_products_id, $product_id);
            } else {
                Log::channel('import')->info("[IMPORT - КОМПАНИЯ][$date] - НЕ найден товар с id='$product_id'");
            }
        }

        if($row[0] == 'name' || $row[1] == 'user_id') return;

        $hash_id = rand(0,9) . rand(100000, 999999) . date('s');

        if(!$company) {
            Log::channel('import')->info("[IMPORT - КОМПАНИЯ][$date] - НЕ найдена компания с name='$row[0]'");

            return new Company([
                'hash_id' => $hash_id, //rand(800000, 999999),
                'name' => $row[0],
                'user_id' => $row[1] ?? 1,
                'note' => $row[2] ?? '',
                'procedure_pv' => $row[3] ?? '',
                'payment_form' => $row[4] ?? '',
                'inn' => $row[5] ?? 0,
                //'req_id' => $row[3] ?? 0,
                'town_id' => $row[6] ?? 0,
                'products_id' => join(',', $valid_products_id),
            ]);
        } else {
            Log::channel('import')->info("[IMPORT - КОМПАНИЯ][$date] - Найдена компания с name='$row[0]'");
        }
    }
}
