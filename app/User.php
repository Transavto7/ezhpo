<?php

namespace App;

use App\Enums\FormTypeEnum;
use App\Enums\UserEntityType;
use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $name
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    const DEFAULT_USER_LOGIN = 'it@nozdratenko.ru';

    public $fillable = [
        'entity_type',
        'photo',
        'email',
        'password',
        'role',
        'blocked',
        'api_token',
        'login',
        'deleted_id',
        'last_connection_at',
        'accepted_agreement',
        'deleted_at',
        'auto_created',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_connection_at' => 'datetime',
    ];

    public static $defaultUserJournalByRole = [
        '1' => FormTypeEnum::TECH,
        '4' => FormTypeEnum::PAK_QUEUE,
    ];

    public function deleted_user(): BelongsTo
    {
        return $this->belongsTo(self::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }

    public function roles($deleted = false): BelongsToMany
    {
        return $this->belongsToMany(Role::class,
            'model_has_roles',
            'model_id',
            'role_id',
            'id',
            'id'
        )->withPivot('deleted')
            ->wherePivot('deleted', $deleted ? 1 : 0);
    }

    public function anketas(): HasMany
    {
        return $this->hasMany(Form::class, 'user_id', 'id')
            ->withDefault();
    }

    public function access(...$permissionName): bool
    {
        return $this->getAllPermissions()
            ->whereIn('name', $permissionName)
            ->isNotEmpty();
    }

    /**
     * // todo: убрать это у пользователя
     */
    public static function getUserCompanyId($field = 'id', $withUserCompanyId = false): int
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        $company = $authUser->relatedCompany;

        if (! $company) {
            return -1;
        }

        $point = $company->pv_id;
        $point = Point::find($point);

        if ($point) {
            $company = $point->company_id ? Company::find($point->company_id) : 0;

            if ($company) {
                return $company->$field;
            }
        }

        if ($withUserCompanyId) {
            return $company->$field;
        }

        return -1;
    }

    public function entity(): HasOne
    {
        $entityType = $this->getAttribute('entity_type');

        if (! $entityType) {
            return $this->hasOne(Employee::class, 'related_user_id', 'id')->whereNull('id');
        }

        switch ($entityType) {
            case UserEntityType::EMPLOYEE:
                return $this->hasOne(Employee::class, 'related_user_id', 'id')->withTrashed();
            case UserEntityType::TERMINAL:
                return $this->hasOne(Terminal::class, 'related_user_id', 'id')->withTrashed();
            case UserEntityType::COMPANY:
                return $this->hasOne(Company::class, 'related_user_id', 'id')->withTrashed();
            case UserEntityType::DRIVER:
                return $this->hasOne(Driver::class, 'related_user_id', 'id')->withTrashed();
        }

        return $this->hasOne(Employee::class, 'related_user_id', 'id')->whereNull('id');
    }

    public function relatedEmployee(): HasOne
    {
        return $this->hasOne(Employee::class, 'related_user_id', 'id')->withTrashed();
    }

    public function relatedTerminal(): HasOne
    {
        return $this->hasOne(Terminal::class, 'related_user_id', 'id')->withTrashed();
    }

    public function relatedCompany(): HasOne
    {
        return $this->hasOne(Company::class, 'related_user_id', 'id')->withTrashed();
    }

    public function relatedDriver(): HasOne
    {
        return $this->hasOne(Driver::class, 'related_user_id', 'id')->withTrashed();
    }

    public function isEmployee(): bool
    {
        return $this->entity_type === UserEntityType::EMPLOYEE;
    }

    public function isTerminal(): bool
    {
        return $this->entity_type === UserEntityType::TERMINAL;
    }

    public function isCompany(): bool
    {
        return $this->entity_type === UserEntityType::COMPANY;
    }

    public function isDriver(): bool
    {
        return $this->entity_type === UserEntityType::DRIVER;
    }

    public function getNameAttribute(): ?string
    {
        if ($this->isDriver()) {
            $relatedEntity = $this->relatedDriver;

            return $relatedEntity !== null ? $relatedEntity->fio : null;
        }

        if ($this->isCompany()) {
            $relatedEntity = $this->relatedCompany;

            return $relatedEntity !== null ? $relatedEntity->name : null;
        }

        if ($this->isTerminal()) {
            $relatedEntity = $this->relatedTerminal;

            return $relatedEntity !== null ? $relatedEntity->name : null;
        }

        if ($this->isEmployee()) {
            $relatedEntity = $this->relatedEmployee;

            return $relatedEntity !== null ? $relatedEntity->name : null;
        }

        return null;
    }

    public function isBlocked(): bool
    {
        return $this->blocked === 1;
    }
}
