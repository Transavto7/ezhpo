<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\WorkReport
 *
 * @property int $id
 * @property int $pv_id
 * @property int $user_id
 * @property string $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport wherePvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport whereWorkBegin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkReport whereWorkEnd($value)
 * @mixin \Eloquent
 */
class WorkReport extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function point(): BelongsTo
    {
        return $this->belongsTo(Point::class, 'pv_id');
    }
}
