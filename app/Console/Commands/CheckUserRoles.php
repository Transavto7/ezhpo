<?php

namespace App\Console\Commands;

use App\GenerateHashIdTrait;
use App\User;
use Closure;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUserRoles extends Command
{
    use GenerateHashIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles
                            {--fix : Исправить ошибки ролей пользователей}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка ролей пользователей';

    /**
     * @var boolean
     */
    private $fixNeeded;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle()
    {
        $this->fixNeeded = $this->option('fix');

        DB::beginTransaction();

        try {
            if ($this->fixNeeded) {
                $this->fixRolesDuplication();
            }

            $this->validateEntitiesWithoutUser(
                'Компании без пользователя',
                $this->findCompanies(),
            );
            $this->validateEntitiesWithoutUserWithRole(
                'Компании без пользователя с ролью client',
                $this->findCompanies(),
                6
            );
            $this->validateEntitiesWithUsersHavingMultipleRoles(
                'Компании с пользователями, у которых несколько ролей (включая роль client)',
                $this->findCompanies(),
                6
            );

            $this->validateEntitiesWithoutUser(
                'Водители без пользователя',
                $this->findDrivers(),
            );
            $this->validateEntitiesWithoutUserWithRole(
                'Водители без пользователя с ролью driver',
                $this->findDrivers(),
                3
            );
            $this->validateEntitiesWithUsersHavingMultipleRoles(
                'Водители с пользователями, у которых несколько ролей (включая роль driver)',
                $this->findDrivers(),
                3
            );

            $this->validateUsersWithoutCompany(
                'Пользователи с ролью client без компании',
                $this->findUsers(),
            );
            $this->validateUsersWithoutDriver(
                'Пользователи с ролью driver без водителя',
                $this->findUsers(),
            );

            DB::commit();

            $this->info('Completed');
        } catch (Exception $e) {
            DB::rollBack();

            $this->error("Ошибка: " . $e->getMessage());
        }
    }

    private function findCompanies(): array
    {
        return DB::table('companies as c')
            ->select([
                'c.id',
                'u.id as user_id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin(
                DB::raw("(select * from users where deleted_at is null) as u"),
                function ($join) {
                    $join->on('u.login', '=', DB::raw("CONCAT('0', c.hash_id)"));
                }
            )
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->whereNull('c.deleted_at')
            ->whereNull('u.deleted_at')
            ->groupBy(['c.id', 'u.id'])
            ->get()
            ->toArray();
    }

    private function findDrivers(): array
    {
        return DB::table('drivers as d')
            ->select([
                'd.id',
                'u.id as user_id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin(
                DB::raw("(select * from users where deleted_at is null) as u"),
                'u.login',
                '=',
                'd.hash_id'
            )
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->whereNull('d.deleted_at')
            ->groupBy(['d.id', 'u.id'])
            ->get()
            ->toArray();
    }

    private function findUsers(): array
    {
        return DB::table('users as u')
            ->select([
                'u.id',
                'u.login',
                'c.id as company_id',
                'd.id as driver_id',
                DB::raw('json_arrayagg(mhr.role_id) as role_ids')
            ])
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->leftJoin(
                DB::raw("(select * from companies as c where deleted_at is null) as c"),
                function ($join) {
                    $join->on('u.login', '=', DB::raw("CONCAT('0', c.hash_id)"));
                }
                )
            ->leftJoin(
                DB::raw("(select * from drivers as d where deleted_at is null) as d"),
                'd.hash_id',
                '=',
                'u.login'
            )
            ->whereNull('u.deleted_at')
            ->groupBy(['u.id', 'u.login', 'c.id', 'd.id'])
            ->get()
            ->toArray();
    }

    private function validateEntitiesWithoutUser(string $title, array $entities)
    {
        $this->iterate(
            $title,
            $entities,
            function ($entity) {
                $roleIds = $this->extractRoles($entity->role_ids);
                return !count($roleIds);
            });
    }

    private function validateEntitiesWithoutUserWithRole(string $title, array $entities, int $requiredRole)
    {
        $this->iterate(
            $title,
            $entities,
            function ($entity) use ($requiredRole) {
                $roleIds = $this->extractRoles($entity->role_ids);
                return !in_array($requiredRole, $roleIds) && count($roleIds);
            }
        );
    }

    private function validateEntitiesWithUsersHavingMultipleRoles(string $title, array $entities, int $requiredRole)
    {
        $this->iterate(
            $title,
            $entities,
            function ($entity) use ($requiredRole) {
                $roleIds = $this->extractRoles($entity->role_ids);
                return in_array($requiredRole, $roleIds) && count($roleIds) > 1;
            },
            function ($entity) use ($title, $requiredRole) {
                $user = User::find($entity->user_id);

                if (!$user) {
                    $this->error('validateEntitiesWithUsersHavingMultipleRoles: user not found');
                    dd($title, $entity);
                }

                $user->roles()
                    ->sync([$requiredRole]);
            }
        );
    }

    private function validateUsersWithoutCompany(string $title, array $entities)
    {
        $this->iterate(
            $title,
            $entities,
            function ($entity) {
                $roleIds = $this->extractRoles($entity->role_ids);
                return in_array(6, $roleIds) && !$entity->company_id;
            }
        );
    }

    private function validateUsersWithoutDriver(string $title, array $entities)
    {
        $this->iterate(
            $title,
            $entities,
            function ($entity) {
                $roleIds = $this->extractRoles($entity->role_ids);
                return in_array(3, $roleIds) && !$entity->driver_id;
            }
        );
    }

    private function fixRolesDuplication() {
        $users = User::whereHas('roles', function ($query) {
            $query->groupBy('role_id', 'model_id')->havingRaw('COUNT(*) > 1');
        })->get();

        foreach ($users as $user) {
            $roles = $user->roles()->get();

            $uniqueRoles = [];

            foreach ($roles as $role) {
                if (!in_array($role->id, $uniqueRoles)) {
                    $uniqueRoles[] = $role->id;
                }
            }

            $user->roles()->detach();
            $user->roles()->attach($uniqueRoles);
        }
    }

    private function iterate(string $title, array $entities, Closure $condition, Closure $actionAfterValidate = null)
    {
        $count = 0;
        $this->comment($title . ':');

        foreach ($entities as $entity) {
            if ($condition($entity)) {
                $separator = $count ? ', ' : '';
                $this->output->write($separator . $entity->id);
                $count++;

                if ($this->fixNeeded && $actionAfterValidate) {
                    $actionAfterValidate($entity);
                }
            }
        }

        if ($count) {
            $this->error("\nНайдено: $count");
            $this->info('');
        } else {
            $this->info("Ничего не найдено\n");
        }
    }

    private function extractRoles($rolesJson): ?array
    {
        if ($rolesJson === null) {
            return null;
        }

        $value = json_decode($rolesJson, true);

        return array_unique(array_filter($value, function ($item) {
            return $item !== null;
        }));
    }
}
