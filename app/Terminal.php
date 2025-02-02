<?php

namespace App;

use App\Enums\UserEntityType;
use App\Traits\HasUserRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Terminal extends Model
{
    use SoftDeletes, HasUserRelationTrait;

    const ENTITY_TYPE = UserEntityType::TERMINAL;

    public $fillable = [
        'blocked',
        'pv_id',
        'api_token',
        'timezone',
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
