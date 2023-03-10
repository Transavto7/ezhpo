<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\DDates
 *
 * @property int $id
 * @property int $hash_id
 * @property string $item_model
 * @property string $field
 * @property int $days
 * @property string $action
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User|null $deleted_user
 * @method static \Illuminate\Database\Eloquent\Builder|DDates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DDates newQuery()
 * @method static Builder|DDates onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DDates query()
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereHashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereItemModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DDates whereUpdatedAt($value)
 * @method static Builder|DDates withTrashed()
 * @method static Builder|DDates withoutTrashed()
 * @mixin \Eloquent
 */
class DDates extends Model
{
    use SoftDeletes;

    public $fillable = [
        'hash_id', 'item_model', 'field', 'days', 'action'
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

    public static function getAll () {
        return self::all();
    }
}
