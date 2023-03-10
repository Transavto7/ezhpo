<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\NotifyStatuse
 *
 * @property int $id
 * @property int $notify_id
 * @property int $user_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|NotifyStatuse newModelQuery()
 * @method static Builder|NotifyStatuse newQuery()
 * @method static Builder|NotifyStatuse query()
 * @method static Builder|NotifyStatuse whereCreatedAt($value)
 * @method static Builder|NotifyStatuse whereId($value)
 * @method static Builder|NotifyStatuse whereNotifyId($value)
 * @method static Builder|NotifyStatuse whereStatus($value)
 * @method static Builder|NotifyStatuse whereUpdatedAt($value)
 * @method static Builder|NotifyStatuse whereUserId($value)
 * @mixin \Eloquent
 */
class NotifyStatuse extends Model
{
    public $fillable = [
        'notify_id', 'status', 'user_id'
    ];
}
