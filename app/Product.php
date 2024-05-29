<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const ESSENCE_DRIVER = 1;
    const ESSENCE_CAR_DRIVER = 3;

    public static $essence = [
        0 => 'Только компанию',
        1 => 'Только водителей',
        2 => 'Только автомобили',
        3 => 'Автомобили или водителей',
    ];

    public $fillable = [
        'hash_id',
        'name',
        'type_product',
        'unit',
        'price_unit',
        'type_anketa',
        'type_view',
        'essence',
        'deleted_id'
    ];

    public function deleted_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class, 'products_id', 'id');
    }

    //TODO: заменить трейтом
    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }

    public function getName($id): string
    {
        $id = explode(',', $id);

        $data = self::query()
            ->select([
                'products.name'
            ])
            ->whereIn('id', $id)
            ->get()
            ->pluck('name')
            ->toArray();

        return implode(', ', $data) ?? '';
    }
}
