<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
                    'message' => $msg
                ]);
            }
        }
    }
}
