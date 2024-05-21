<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class Role extends \Spatie\Permission\Models\Role
{
    use SoftDeletes;

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function permissions($deleted = false): BelongsToMany
    {
        return $this->belongsToMany(Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id',
            'id',
            'id'
        )
            ->withPivot('deleted')
            ->wherePivot('deleted', $deleted ? 1 : 0);
    }

    public function users($deleted = false): BelongsToMany
    {
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
