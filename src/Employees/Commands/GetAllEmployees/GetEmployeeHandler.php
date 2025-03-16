<?php
declare(strict_types=1);

namespace Src\Employees\Commands\GetAllEmployees;

use App\Settings;
use Illuminate\Support\Facades\DB;
use Src\Employees\Workdays\Eloquent\Workday;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;
use Symfony\Component\HttpFoundation\Response;

final class GetEmployeeHandler
{
    public function handle(GetEmployeeCommand $getEmployeeCommand): array
    {
        $settingDefaultValues = [
            'pressure_systolic' => Settings::DEFAULT_PRESSURE_SYSTOLIC,
            'pressure_diastolic' => Settings::DEFAULT_PRESSURE_DIASTOLIC,
            'pulse_lower' => Settings::DEFAULT_PULSE_LOWER,
            'pulse_upper' => Settings::DEFAULT_PULSE_UPPER,
            'time_of_alcohol_ban' => Settings::DEFAULT_TIME_OF_ALCOHOL_BAN,
            'time_of_pressure_ban' => Settings::DEFAULT_TIME_OF_PRESSURE_BAN
        ];

        $settings = DB::table('settings')
            ->select([
                'settings.key',
                'settings.value'
            ])
            ->whereIn('settings.key', array_keys($settingDefaultValues))
            ->get()
            ->toArray();
        $settings = array_combine(array_column($settings, 'key'), array_column($settings, 'value'));

        foreach ($settingDefaultValues as $key => $defaultValue) {
            if (empty($settings[$key])) {
                $settings[$key] = $defaultValue;
            } else {
                $settings[$key] = (int)$settings[$key];
            }
        }

        $employeeList = DB::table('users')
            ->select([
                'users.hash_id',
                'users.name'
            ])
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->whereIn('model_has_roles.role_id', [1, 2])
            ->whereNull('users.deleted_at');

        if ($filterHashId = $getEmployeeCommand->getFilterHashId()) {
            $employeeList = $employeeList->where('users.hash_id', '=', $filterHashId);
        }

        $employeeList = $employeeList
            ->get()
            ->toArray();

        $result = [];
        foreach ($employeeList as $employee) {
            $result[] = [
                'pressure_systolic' => $settings['pressure_systolic'],
                'pulse_upper' => $settings['pulse_upper'],
                'end_of_ban' => '',
                'time_of_alcohol_ban' => $settings['time_of_alcohol_ban'],
                'pulse_lower' => $settings['pulse_lower'],
                'dismissed' => false,
                'time_of_pressure_ban' => $settings['time_of_pressure_ban'],
                'fio' => $employee->name ?? '',
                'pressure_diastolic' => $settings['pressure_diastolic'],
                'hash_id' => (int)($employee->hash_id ?? 0),
            ];
        }

        // Валидация ответа только если ищем одного сотрудника, с фильтром
        if ($filterHashId) {
            if (empty($result)) {
                throw new \Exception('Сотрудник с указанным ID не найден!', Response::HTTP_BAD_REQUEST);
            } elseif ($result[0]['dismissed']) {
                throw new \Exception('Сотрудник с указанным ID уволен!', Response::HTTP_SEE_OTHER);
            }

            // Проверка на дубликат в этот же день
            $existingWorkday = Workday::query()
                ->leftJoin('users', 'users.id', '=', 'workdays.employee_id')
                ->where('users.hash_id', $filterHashId)
                ->whereDate('date', now()->format('Y-m-d'))
                ->orderBy('workdays.created_at', 'desc')
                ->first();
            if ($existingWorkday) {
                if ($existingWorkday->type_anketa === WorkdayEventTypeEnum::CLOSE) {
                    throw new \Exception('Сотрудник уже имеет запись в этот день', Response::HTTP_BAD_REQUEST);
                }

                if ($existingWorkday->type_anketa === WorkdayEventTypeEnum::OPEN) {
                    $result[0]['inspection_types'] = [
                        WorkdayEventTypeEnum::create(WorkdayEventTypeEnum::CLOSE)->getSpdoValue(),
                    ];
                }
            } else {
                $result[0]['inspection_types'] = [
                    WorkdayEventTypeEnum::create(WorkdayEventTypeEnum::OPEN)->getSpdoValue()
                ];
            }

        }


        return $result;
    }
}
