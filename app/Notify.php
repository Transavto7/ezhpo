<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Notify
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $message
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $role
 * @method static Builder|Notify newModelQuery()
 * @method static Builder|Notify newQuery()
 * @method static Builder|Notify query()
 * @method static Builder|Notify whereCreatedAt($value)
 * @method static Builder|Notify whereId($value)
 * @method static Builder|Notify whereMessage($value)
 * @method static Builder|Notify whereRole($value)
 * @method static Builder|Notify whereStatus($value)
 * @method static Builder|Notify whereUpdatedAt($value)
 * @method static Builder|Notify whereUserId($value)
 * @mixin \Eloquent
 */
class Notify extends Model
{
    public $fillable = [
        'user_id', 'message', 'status', 'role'
    ];

    public function sendMsgToUsersFrom ($field, $value, $msg) {
        $users = User::where($field, $value)->get();

        if($users) {
            foreach($users as $user) {
                return $this->create([
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'message' => $msg
                ]);
            }
        }
    }
}
