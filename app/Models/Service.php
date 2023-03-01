<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Service
 *
 * @property int $id
 * @property string $hash_id
 * @property string $name
 * @property string|null $type_product
 * @property string $unit
 * @property int $price_unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $type_anketa
 * @property string|null $type_view
 * @property int|null $essence
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $deleted_id
 * @property-read User|null $deleted_user
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Query\Builder|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereEssence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereHashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePriceUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereTypeAnketa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereTypeProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereTypeView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Service withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Service withoutTrashed()
 * @mixin \Eloquent
 */
class Service extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
//    protected $table = 'services';
    protected $table = 'products';

    protected $guarded = [];


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
}
