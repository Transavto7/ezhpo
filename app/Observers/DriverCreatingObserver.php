<?php

namespace App\Observers;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Instr;
use App\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DriverCreatingObserver
{
    public function created(Driver $driver) {
        $products = $driver->products_id;
        $products = explode(",", $products);

        $productsEntities = Product::whereIn("id", $products)->pluck("hash_id")->toArray();
        /** @var $defaultBriefing Object Hash ID базового инструктажа */
        $defaultBriefing = Instr::where("is_default", true)->pluck("hash_id", "name");

        if (in_array(570316, $productsEntities) || in_array(199217, $productsEntities)) {
            $user = Auth::user();
            $company = Company::where("hash_id", $driver->company_id)->first();

            Anketa::create([
                "type_anketa" => "bdd",
                "user_id"     => $user->id,
                "user_name"   => $user->name,
                "driver_id"   => $driver->hash_id,
                "driver_fio"  => $driver->fio,
                "driver_gender" => $driver->gender,
                "driver_year_birthday" => $driver->year_birthday,
                "complaint" => "Нет",
                "condition_visible_sliz" => "Без особенностей",
                "condition_koj_pokr" => "Без особенностей",
                "date" => Carbon::now(),
                "type_view" => "Предрейсовый",
                "company_id" => $driver->company_id,
                "company_name" => $company->name,
                "briefing_name" => $defaultBriefing->name
           ]);
        }
    }
}
