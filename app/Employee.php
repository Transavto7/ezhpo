<?php

namespace App;

use App\Enums\UserEntityType;
use App\Traits\HasUserRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Employee extends Model
{
    use SoftDeletes, HasUserRelationTrait;

    const ENTITY_TYPE = UserEntityType::EMPLOYEE;

    public $fillable = [
        'blocked',
        'pv_id',
        'eds',
        'timezone',
        'validity_eds_start',
        'validity_eds_end',
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
