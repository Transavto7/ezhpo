<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

//    protected $table = 'services';
    protected $table = 'products';

    public $fillable = [
        'hash_id', 'name', 'type_product', 'unit', 'price_unit',
        'type_anketa', 'type_view', 'essence',
        'deleted_id'
    ];

//    public function __construct(array $attributes = [])
//    {
//        parent::__construct($attributes);
//    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    // Сущности
    public static $essence = [
        0 => 'Только компанию',
        1 => 'Только водителей',
        2 => 'Только автомобили',
        3 => 'Автомобили или водителей',
    ];

    const ESSENCE_COMPANY    = 0;
    const ESSENCE_DRIVER     = 1;
    const ESSENCE_CAR        = 2;
    const ESSENCE_CAR_DRIVER = 3;

    // многие ко многим ебать
    public function getName ($id)
    {
        $id = explode(',', $id);

        $data = self::whereIn('id', $id)->get();

        if(!$data) {
            $data = '';
        } else {
            $newData = '';

            foreach($data as $dataItemKey => $dataItem) {
                $newData .= ($dataItemKey !== 0 ? ', ' : '') . $dataItem->name;
            }

            $data = $newData;
        }

        return $data;
    }
    // это чё блять за хуйня блят ь
    public static function getAll () {
        return self::all();
    }
}