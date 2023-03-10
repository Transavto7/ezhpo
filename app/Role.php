<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Spatie\Permission\Models\Permission;

/**
 * App\Role
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User|null $deleted_user
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static Builder|Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role withTrashed()
 * @method static Builder|Role withoutTrashed()
 * @mixin \Eloquent
 */
class Role extends \Spatie\Permission\Models\Role
{
    use SoftDeletes;

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function permissions($deleted = false) : BelongsToMany
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

    public function users($deleted = false) : BelongsToMany
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
