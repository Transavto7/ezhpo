<?php

namespace App;

use App\Enums\UserEntityType;
use App\Traits\HasUserRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Terminal extends Model
{
    use SoftDeletes, HasUserRelation;

    const ENTITY_TYPE = UserEntityType::TERMINAL;

    public $fillable = [
        'name',
        'blocked',
        'pv_id',
        'stamp_id',
        'last_connection_at',
        'auto_created',
        'deleted_at',
        'deleted_id',
    ];

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }
}
