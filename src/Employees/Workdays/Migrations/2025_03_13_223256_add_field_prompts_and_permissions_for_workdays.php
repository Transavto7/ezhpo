<?php

use App\FieldPrompt;
use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class AddFieldPromptsAndPermissionsForWorkdays extends Migration
{
    const PERMISSIONS = [
        'employees_workdays_create' => 'Рабочие смены - Создание',
        'employees_workdays_read' => 'Рабочие смены - Просмотр',
        'employees_workdays_report' => 'Рабочие смены - Отчет',
        'employees_workdays_holidays' => 'Рабочие смены - Редактирование нерабочих дней',
        'employees_workdays_tariffs' => 'Рабочие смены - Редактирование тарифов',
    ];

    const ROLES = [
        'admin',
    ];

    const FIELDS = [
        'date' => [
            'name' => 'Дата осмотра',
            'description' => 'Дата и время открытия\закрытия смены на СДПО или вручную указанная дата на ЭЖПО.',
        ],
        'employee_id' => [
            'name' => 'Сотрудник',
            'description' => 'ФИО сотрудника, открывающего\закрывающего смену.',
        ],
        'point_id' => [
            'name' => 'ПВ',
            'description' => 'Пункт выпуска, на котором отработана смена.',
        ],
        'type_anketa' => [
            'name' => 'Тип осмотра',
            'description' => 'Открытие или закрытие смены.',
        ],
        'flag_pak' => [
            'name' => 'Флаг СДПО',
            'description' => 'СДПО-А осмотр или добавление записи администратором.',
        ],
        'is_real' => [
            'name' => 'Осмотр реальный',
            'description' => 'Осмотр проведен в момент создания записи или "задним числом".',
        ],
        'admitted' => [
            'name' => 'Допуск',
            'description' => 'Заключение о допуске к работе.',
        ],
        'photo' => [
            'name' => 'Фото',
            'description' => 'Фото осмотра.',
        ],
        'video' => [
            'name' => 'Видео',
            'description' => 'Видео осмотра.',
        ],
        't_people' => [
            'name' => 'Температура тела',
            'description' => 'Температура тела.',
        ],
        'pressure_systolic' => [
            'name' => 'Верхнее АД',
            'description' => 'Систолическое артериальное давление.',
        ],
        'pressure_diastolic' => [
            'name' => 'Нижнее АД',
            'description' => 'Диастолическое артериальное давление.',
        ],
        'pulse' => [
            'name' => 'Пульс',
            'description' => 'Пульс.',
        ],
        'narko_test_status' => [
            'name' => 'Тест на наркотики',
            'description' => 'Результат тестирования на наркотические вещества.',
        ],
        'alcometer_result' => [
            'name' => 'Уровень алкоголя в выдыхаемом воздухе',
            'description' => 'Уровень алкоголя в выдыхаемом воздухе.',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            foreach (self::FIELDS as $field => $attributes) {
                FieldPrompt::updateOrCreate([
                    'type' => 'workdays',
                    'field' => $field,
                ], array_merge($attributes, ['deleted_at' => null]));
            }

            /** @var Role $role */
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
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::beginTransaction();

        try {
            FieldPrompt::query()->where('type', 'workdays')->delete();

            $permissionClass = app(PermissionContract::class);

            foreach (self::PERMISSIONS as $slug => $title) {
                $permission = $permissionClass::findOrCreate($slug, $title);

                $permission->forceDelete();
            }
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
