<?php

namespace App\Console\Commands;

use App\GenerateHashIdTrait;
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

        $companies = DB::table('companies as c')
            ->select([
                'c.id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin('users as u', function ($join) {
                $join->on('u.login', '=', DB::raw("'0' + c.hash_id"));
            })
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->whereNull('c.deleted_at')
            ->groupBy(['c.id'])
            ->get()
            ->toArray();

        $this->validateEntitiesWithoutUser($companies, 'Компании без пользователя');
        $this->validateEntitiesWithoutUserWithRole($companies, 'Компании без пользователя с ролью client', 6);
        $this->validateEntitiesWithUsersHavingMultipleRoles($companies, 'Компании с пользователями, у которых несколько ролей (включая роль client)', 6);

        $drivers = DB::table('drivers as d')
            ->select([
                'd.id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin('users as u', 'u.login', '=', 'd.hash_id')
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->whereNull('d.deleted_at')
            ->groupBy(['d.id'])
            ->get()
            ->toArray();

        $this->validateEntitiesWithoutUser($drivers, 'Водители без пользователя');
        $this->validateEntitiesWithoutUserWithRole($drivers, 'Водители без пользователя с ролью driver', 3);
        $this->validateEntitiesWithUsersHavingMultipleRoles($drivers, 'Водители с пользователями, у которых несколько ролей (включая роль driver)', 3);

        $users = DB::table('users as u')
            ->select([
                'u.id',
                'u.login',
                'c.id as company_id',
                'd.id as driver_id',
                DB::raw('json_arrayagg(mhr.role_id) as role_ids')
            ])
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->leftJoin('companies as c', function ($join) {
                $join->on(DB::raw("'0' + c.hash_id"), '=', 'u.login');
            })
            ->leftJoin('drivers as d', 'd.hash_id', '=', 'u.login')
            ->whereNull('u.deleted_at')
            ->groupBy(['u.id', 'u.login'])
            ->get()
            ->toArray();

        $this->validateUsersWithoutCompany($users, 'Пользователи с ролью client без компании');
        $this->validateUsersWithoutDriver($users, 'Пользователи с ролью driver без водителя');

        $this->info('Completed');
    }

    private function validateEntitiesWithoutUser(array $entities, string $title)
    {
        $this->iterate(
            $entities,
            $title,
            function ($entity) {
                $roleIds = $this->castJson($entity->role_ids);
                return !count($roleIds);
            }
        );
    }

    private function validateEntitiesWithoutUserWithRole(array $entities, string $title, int $requiredRole)
    {
        $this->iterate(
            $entities,
            $title,
            function ($entity) use ($requiredRole) {
                $roleIds = $this->castJson($entity->role_ids);
                return !in_array($requiredRole, $roleIds) && count($roleIds);
            }
        );
    }

    private function validateEntitiesWithUsersHavingMultipleRoles(array $entities, string $title, int $requiredRole)
    {
        $this->iterate(
            $entities,
            $title,
            function ($entity) use ($requiredRole) {
                $roleIds = $this->castJson($entity->role_ids);
                return in_array($requiredRole, $roleIds) && count($roleIds) > 1;
            }
        );
    }

    private function validateUsersWithoutCompany(array $entities, string $title)
    {
        $this->iterate(
            $entities,
            $title,
            function ($entity) {
                $roleIds = $this->castJson($entity->role_ids);
                return in_array(6, $roleIds) && !$entity->company_id;
            }
        );
    }

    private function validateUsersWithoutDriver(array $entities, string $title)
    {
        $this->iterate(
            $entities,
            $title,
            function ($entity) {
                $roleIds = $this->castJson($entity->role_ids);
                return in_array(3, $roleIds) && !$entity->driver_id;
            }
        );
    }

    private function iterate(array $entities, string $title, Closure $condition, Closure $actionAfterValidate = null)
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
            $this->error("\nНайдено: $count\n");
        }
        else {
            $this->info("Ничего не найдено\n");
        }
    }

    private function castJson($rawValue): ?array
    {
        if ($rawValue === null) {
            return null;
        }

        $value = json_decode($rawValue, true);

        return array_unique(array_filter($value, function ($item) {
            return $item !== null;
        }));
    }
}
