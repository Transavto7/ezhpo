<?php

namespace App\Console\Commands;

use App\GenerateHashIdTrait;
use App\Repositories\CheckUserRoles\CheckUserRolesEntityRepository;
use App\User;
use Closure;
use DateTimeImmutable;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CheckUserRoles extends Command
{
    use GenerateHashIdTrait;
    const FIXED_DUPLICATES = 'fixed_duplicates';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles
                            {--fix : Исправление ошибок}
                            {--c|--companies : Валидация компаний}
                            {--d|--drivers : Валидация водителей}
                            {--u|--users : Валидация пользователей}
                            {--r|--duplicated-roles : Валидация пользователей с повторяющимися ролями}
                            {--s|--show-users-with-duplicated-roles : Отображение ID пользователей с дублирующимися ролями}';

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
     * @var CheckUserRolesEntityRepository
     */
    private $entityRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CheckUserRolesEntityRepository $entityRepository)
    {
        parent::__construct();
        $this->entityRepository = $entityRepository;
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
            if ($this->option('duplicated-roles')) {
                $this->validateUsersWithDuplicatedRoles($this->entityRepository->findUsersWithDuplicatedRoles());
            }

            if ($this->option('companies')) {
                $companies = $this->entityRepository->findCompanies();

                $this->validateCompaniesWithoutUser('Компании без пользователя', $companies,);
                $this->validateEntitiesWithoutUserWithRole('Компании с пользователем, у которого нет роли client', $companies, 6);
                $this->validateEntitiesWithUsersHavingMultipleRoles(
                    'Компании с пользователями, у которых несколько ролей (включая роль client)',
                    $companies,
                    6
                );
            }

            if ($this->option('drivers')) {
                $drivers = $this->entityRepository->findDrivers();

                $this->validateDriversWithoutUser('Водители без пользователя', $drivers,);
                $this->validateEntitiesWithoutUserWithRole('Водители с пользователем, у которого нет роли driver', $drivers, 3);
                $this->validateEntitiesWithUsersHavingMultipleRoles(
                    'Водители с пользователями, у которых несколько ролей (включая роль driver)',
                    $drivers,
                    3
                );
            }

            if ($this->option('users')) {
                $users = $this->entityRepository->findUsers();

                $this->validateUsersWithoutCompany('Пользователи с ролью client без компании', $users);
                $this->validateUsersWithoutDriver('Пользователи с ролью driver без водителя', $users);
            }

            DB::commit();

            $this->info('Completed');
        } catch (Exception $e) {
            DB::rollBack();

            $this->error("Ошибка: " . $e->getMessage());
        }
    }

    private function validateUsersWithDuplicatedRoles(array $entities) {
        $logIds = $this->option('show-users-with-duplicated-roles');

        $this->iterate(
            'Пользователи, с повторяющимися ролями',
            $entities,
            function () {
                return true;
            },
            function ($entity) {
                $user = User::find($entity->id);
                $roles = $user->roles()->get();

                $uniqueRoles = [];

                foreach ($roles as $role) {
                    if (!in_array($role->id, $uniqueRoles)) {
                        $uniqueRoles[] = $role->id;
                    }
                }

                $user->roles()->detach();
                $user->roles()->attach($uniqueRoles);
            },
            $logIds
        );
    }

    private function validateCompaniesWithoutUser(string $title, array $companies)
    {
        $this->iterate(
            $title,
            $companies,
            function ($company) {
                $roleIds = $this->extractRoles($company->role_ids);
                return !count($roleIds);
            },
            function ($company) {
                $this->createUser(
                    $company->hash_id,
                    $company->name,
                    $company->id,
                    6,
                    12,
                    '0'
                );
            });
    }

    private function validateDriversWithoutUser(string $title, array $drivers)
    {
        $this->iterate(
            $title,
            $drivers,
            function ($driver) {
                $roleIds = $this->extractRoles($driver->role_ids);
                return !count($roleIds);
            },
            function ($driver) {
                $this->createUser(
                    $driver->hash_id,
                    $driver->fio,
                    $driver->company_id,
                    3,
                    3
                );
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
                    throw new Exception('validateEntitiesWithUsersHavingMultipleRoles: user not found');
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
            },
            function ($entity) use ($title) {
                $user = User::find($entity->id);

                if (!$user) {
                    throw new Exception("validateUsersWithoutCompany: user {$entity->id} not found");
                }

                $user->deleted_at = new DateTimeImmutable();
                $user->save();
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
            },
            function ($entity) use ($title) {
                $user = User::find($entity->id);

                if (!$user) {
                    throw new Exception("validateUsersWithoutDriver: user {$entity->id} not found");
                }

                $user->deleted_at = new DateTimeImmutable();
                $user->save();
            }
        );
    }

    private function iterate(
        string  $title,
        array   $entities,
        Closure $condition,
        Closure $fixAction = null,
        bool    $logIds = true
    )
    {
        $count = 0;
        $fixedCount = 0;
        $this->comment($title . ':');

        foreach ($entities as $entity) {
            if ($condition($entity)) {
                if ($this->fixNeeded && $fixAction) {
                    $fixAction($entity);
                    $fixedCount++;
                }
                else {
                    if ($logIds) {
                        $separator = $count ? ', ' : '';
                        $this->output->write($separator . $entity->id);
                    }

                    $count++;
                }
            }
        }

        if ($count && $logIds) {
            $this->line('');
        }

        if ($count) {
            $this->error("Найдено: $count");
            $this->line('');
        } else if ($this->fixNeeded) {
            $this->info("Исправлено: $fixedCount\n");
        } else {
            $this->info("Ничего не найдено\n");
        }
    }

    private function extractRoles($rolesJson): array
    {
        if ($rolesJson === null) {
            return [];
        }

        $value = json_decode($rolesJson, true);

        return array_unique(array_filter($value, function ($item) {
            return $item !== null;
        }));
    }

    /**
     * @throws Exception
     */
    protected function createUser(
        string $hashId,
        string $name,
        string $companyId,
        int $role,
        int $roleUser,
        string $loginPrefix = '')
    {
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

        $userLogin = $loginPrefix . $hashId;
        $email = $hashId . '-' . $userHashId . '@ta-7.ru';

        $user = User::create([
            'hash_id' => $userHashId,
            'email' => $email,
            'api_token' => Hash::make(date('H:i:s') . sha1($hashId)),
            'login' => $userLogin,
            'password' => Hash::make($userLogin),
            'name' => $name,
            'role' => $roleUser,
            'company_id' => $companyId
        ]);

        $user->roles()->attach($role);

        $this->info("Добавлен новый пользователь (login: $email)");
    }
}
