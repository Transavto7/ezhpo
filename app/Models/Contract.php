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
        FieldPrompt::where('field', 'products_id')->where('type', 'company')->delete();
        FieldPrompt::where('field', 'products_id')->where('type', 'car')->delete();
        FieldPrompt::where('field', 'products_id')->where('type', 'driver')->delete();

        $products = Product::get()->toArray();

        Service::insert($products);

        FieldPrompt::create([
            'field' => 'service_id',
            'type' => 'car',
        ]);
        FieldPrompt::create([
            'field' => 'service_id',
            'type' => 'driver',
        ]);

        return true;
    }

}
