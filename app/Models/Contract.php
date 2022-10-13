<?php

namespace App\Models;

use App\Car;
use App\Company;
use App\Driver;
use App\FieldPrompt;
use App\Product;
use App\Req;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'contracts';

    protected $casts = [
        'date_of_end' => 'datetime'
    ];

    protected $guarded = [];

    public static $types = [
        1 => 'Абонентская плата',
        2 => 'Разовая',
    ];

    // main contract for company
    function scopeMainForCompany($query, $contract_id)
    {
        return $query->where('company_id', $contract_id)
                     ->orderBy("main_for_company",'DESC')
                     ->first();
    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function company()
    {
        return $this->belongsTo(
            Company::class,
            'company_id',
            'id'
        )->withDefault();
    }

    public function our_company()
    {
        return $this->belongsTo(
            Req::class,
            'our_company_id',
            'id'
        )->withDefault();
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'contract_service',
            'contract_id',
            'service_id',
            'id',
            'id'
        )->withPivot('service_cost');
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();
        return parent::delete();
    }


    public static function startContract(){
//        FieldPrompt::where('field', 'products_id')->where('type', 'company')->delete();
//        FieldPrompt::where('field', 'products_id')->where('type', 'car')->delete();
//        FieldPrompt::where('field', 'products_id')->where('type', 'driver')->delete();

//        $products = Product::get()->toArray();
//
//        Service::insert($products);

//        FieldPrompt::create([
//            'field' => 'service_id',
//            'type' => 'car',
//        ]);
//        FieldPrompt::create([
//            'field' => 'service_id',
//            'type' => 'driver',
//        ]);
        FieldPrompt::create([
            'field' => 'contracts',
            'type' => 'company',
            'name' => 'Договор',
        ]);


//        ini_set('memory_limit', '-1');
//        ini_set('max_execution_time', 400);
//        set_time_limit(400);
//
//        $companies = Company::whereNotNull('products_id')
//                            ->get(['id', 'products_id']);
//        $services = Service::get();
//
//        $comp_products_arr = [];
//        foreach ($companies as $company){
//            $services_item = explode(',', $company->products_id);
//            $res = [];
//            foreach ($services_item as $item){
//                if($tar = $services->where('id', $item)->first()){
//                    $res[$tar->id] =  ['service_cost' => $tar->price_unit];
////                        [$tar->id => ['service_cost' => $tar->price_unit]];
//                }
//            }
//
//            $comp_products_arr[$company->id] = $res;
//        }
////        dd($comp_products_arr);
//        foreach ($comp_products_arr as $company_id => $services_item){
//            if(!$services_item){
//                continue;
//            }
////            if(!(($services_item['pr_id'] ?? false) && ($services_item['sync'] ?? false))){
////                continue;
////            }
//            if($contract = Contract::where('company_id', $company_id)->first()){
//                $contract->services()->sync($services_item);
//                continue;
//            }
//            $contract = Contract::create([
//                'name' => "Договор $company_id",
//                'company_id' => $company_id
//            ]);
//
//            $contract->services()->sync($services_item);
//
//            Driver::where('company_id', $company_id)->update([
//                'contract_id' => $contract->id
//            ]);
//            Car::where('company_id', $company_id)->update([
//                'contract_id' => $contract->id
//            ]);
//        }



        return true;
    }

}
