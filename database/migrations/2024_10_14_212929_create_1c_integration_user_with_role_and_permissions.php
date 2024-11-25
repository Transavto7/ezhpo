<?php

use App\GenerateHashIdTrait;
use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Contracts\Role as RoleContract;

class Create1cIntegrationUserWithRoleAndPermissions extends Migration
{
    use GenerateHashIdTrait;

    const INTEGRATION_1C_USER_LOGIN = 'integration_1c';
    const INTEGRATION_1C_ROLE = 'integration_1c';

    const ROLES = [
        'admin',
        self::INTEGRATION_1C_ROLE,
    ];

    const PERMISSIONS = [
        'integration_1c_read' => 'Интеграция 1С - чтение данных',
        'integration_1c_write' => 'Интеграция 1С - добавление и изменение данных',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            $integration1CRole = $this->createRole();
            $this->createUser($integration1CRole->id);

            $roles = Role::query()
                ->whereIn('name', self::ROLES)
                ->get();

            if ($roles->count() !== count(self::ROLES)) {
                throw new Exception('Some roles not found!');
            }

            /** @var User $user */
            $user = User::query()
                ->withoutGlobalScopes()
                ->where('login', User::DEFAULT_USER_LOGIN)
                ->first();

            if (empty($user)) {
                throw new Exception('Default admin user does not exists!');
            }

            $permissionClass = app(PermissionContract::class);

            foreach (self::PERMISSIONS as $slug => $title) {
                $permission = $permissionClass::findOrCreate($slug, $title);

                $permissionId = $permission->id;

                $roles->each(function ($role) use ($permissionId) {
                    $role->permissions()->syncWithoutDetaching([$permissionId]);
                });

                $user->permissions()->syncWithoutDetaching([$permissionId]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws DateMalformedStringException
     */
    public function down()
    {
        DB::beginTransaction();

        try {
            $permissionClass = app(PermissionContract::class);

            foreach (self::PERMISSIONS as $slug => $title) {
                $permission = $permissionClass::findOrCreate($slug, $title);

                $permission->forceDelete();
            }

            $roleClass = app(RoleContract::class);
            $role = $roleClass::findOrCreate(self::INTEGRATION_1C_ROLE, 'Интеграция 1С');
            $role->forceDelete();

            $user = User::query()
                ->withoutGlobalScopes()
                ->where('login', self::INTEGRATION_1C_USER_LOGIN)
                ->first();

            if ($user) {
                $user->deleted_at = new DateTimeImmutable();
                $user->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createRole()
    {
        $roleClass = app(RoleContract::class);
        return $roleClass::findOrCreate(self::INTEGRATION_1C_ROLE, 'Интеграция 1С');
    }

    /**
     * @throws Exception
     */
    private function createUser(int $role)
    {
        $user = User::query()
            ->withoutGlobalScopes()
            ->where('login', self::INTEGRATION_1C_USER_LOGIN)
            ->first();

        if ($user) {
            $user->deleted_at = null;
            $user->role = $role;
            $user->save();
            $user->roles()->attach($role);

            return;
        }

        $validator = function (int $hashId) {
            if (User::where('hash_id', $hashId)->first()) {
                return false;
            }

            return true;
        };

        $userHashId = $this->generateHashId(
            $validator,
            config('app.hash_generator.user.min'),
            config('app.hash_generator.user.max'),
            config('app.hash_generator.user.tries')
        );

        $user = User::create([
            'hash_id' => $userHashId,
            'email' => self::INTEGRATION_1C_USER_LOGIN . '@ta-7.ru',
            'api_token' => Hash::make(date('H:i:s') . sha1($userHashId)),
            'login' => self::INTEGRATION_1C_USER_LOGIN,
            'password' => Hash::make(self::INTEGRATION_1C_USER_LOGIN),
            'name' => 'Интеграция 1С',
            'role' => $role
        ]);

        $user->roles()->attach($role);
    }
}
