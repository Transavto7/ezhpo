<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;

class Role extends \Spatie\Permission\Models\Role
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function permissions($deleted = false) : BelongsToMany
    {
        // pizdec huita///.....// prosto nahui relationship
        return $this->belongsToMany(Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id',
            'id',
            'id'
        )->withPivot('deleted')
                    ->wherePivot('deleted', $deleted ? 1 : 0);
    }

    public function users($deleted = false) : BelongsToMany
    {
        // pizdec huita///.....// prosto nahui relationship
        return $this->belongsToMany(User::class,
            'model_has_roles',
            'role_id',
            'model_id',
            'id',
            'id'
        )->withPivot('deleted')
                    ->wherePivot('deleted', $deleted ? 1 : 0);
    }
}
