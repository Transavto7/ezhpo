<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\Discount
 *
 * @property int $id
 * @property string $hash_id
 * @property string|null $products_id
 * @property float $discount
 * @property int|null $porog
 * @property string $trigger
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $deleted_id
 * @property-read \App\User|null $deleted_user
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newQuery()
 * @method static Builder|Discount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereHashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount wherePorog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereProductsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereTrigger($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereUpdatedAt($value)
 * @method static Builder|Discount withTrashed()
 * @method static Builder|Discount withoutTrashed()
 * @mixin \Eloquent
 */
class Discount extends Model
{
    use SoftDeletes;

    public $fillable = [
        'hash_id',
        'products_id',
        'trigger',
        'porog',
        'discount',
        'deleted_id'
    ];

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();
        return parent::delete();
    }

    public function getDiscount($total) {
        $is_discount_valid = false;
        eval('$is_discount_valid = ' . $total . $this->trigger . $this->porog . ';');

        if ($is_discount_valid) {
            return $this->discount;
        }

        return false;
    }
}
