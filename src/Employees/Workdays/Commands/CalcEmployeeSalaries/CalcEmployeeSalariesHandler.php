<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class CalcEmployeeSalariesHandler
{
    public function handle(CalcEmployeeSalariesCommand  $command): array
    {
        if (!$command->getDateFrom() || !$command->getDateTo()) {
            throw new \Exception('Даты заданы неверно', Response::HTTP_BAD_REQUEST);
        }

        $dateFromString = $command->getDateFrom()->format('Y-m-d H:i:s');
        $dateToString = $command->getDateTo()->format('Y-m-d H:i:s');

        $tariffList = DB::select("
SELECT t.town_id,
       t.point_id,
       t.role_id,
       t.price_cfg,
       t.date_from,
       t.date_to,
       th.hour,
       th.price
FROM tariffs t
         INNER JOIN tariff_hours th ON th.tariff_id = t.id
WHERE (t.date_from <= '{$dateFromString}' AND t.date_to >= '{$dateFromString}')
   OR (t.date_from > '{$dateFromString}' AND t.date_to < '{$dateToString}')
   OR (t.date_from <= '{$dateToString}}' AND t.date_to >= '{$dateToString}}')
ORDER BY t.date_from");

        $settingList = new CalcEmployeeSalarySettingList();

        CalcEmployeeSalary::setCalcEmployeeSalarySettingList($settingList);

        foreach ($tariffList as $row) {
            $settingList->addRow(
                $row->town_id,
                $row->point_id,
                $row->role_id,
                $row->price_cfg,
                $row->date_from,
                $row->date_to,
                $row->hour,
                $row->price,
            );
        }

        $workdayRows = DB::select("
SELECT STR_TO_DATE(DATE_FORMAT(w_open.date, '%Y-%m-%d %H'), '%Y-%m-%d %H') as 'datetime_round_open',
       STR_TO_DATE(DATE_FORMAT(IF(MINUTE(w_close.date) > 0 or SECOND(w_close.date) > 0,
                                  DATE_ADD(w_close.date, INTERVAL 1 HOUR), w_close.date), '%Y-%m-%d %H'),
                   '%Y-%m-%d %H')                                          as 'datetime_round_close',
       w_open.date                                                         as 'datetime_open',
       w_close.date                                                        as 'datetime_close',
       w_open.employee_id,
       mhr.role_id,
       p.id                                                                as 'point_id',
       IF(p.pv_id is null or p.pv_id = 0, null, p.pv_id)                   as 'town_id',
       h.id                                                                as 'is_holiday',
       u.name                                                              as 'employee_name'
FROM workdays w_open
         INNER JOIN users u on u.id = w_open.employee_id
         LEFT JOIN points p on p.pv_id = u.pv_id
         INNER JOIN model_has_roles mhr on mhr.model_id = w_open.employee_id and mhr.role_id in (1, 2)
         LEFT JOIN workdays w_close
                    on w_open.employee_id = w_close.employee_id and w_open.id = w_close.open_workday_id and
                       w_close.admitted = 1
         LEFT JOIN holidays h on h.date = STR_TO_DATE(DATE_FORMAT(w_open.date, '%Y-%m-%d'), '%Y-%m-%d')
WHERE w_open.admitted = 1
  AND w_open.type_anketa = 1
  AND w_open.date >= '{$dateFromString}'
  AND w_open.date <= '{$dateToString}'");

        $employees = [];
        $errorReport = [
            'workdayErrors' => [],
            'pointList' => [],
            'townList' => [],
            'roleList' => []
        ];

        $registerError = static function (string $message, $row) use (&$errorReport) {
            if (empty($errorReport['workdayErrors'][$row->employee_id])) {
                $errorReport['workdayErrors'][$row->employee_id] = [];
            }

            $errorReport['workdayErrors'][$row->employee_id][] = [
                'employeeId' => $row->employee_id,
                'errorMessage' => $message,
                'dateTimeOpen' => $row->datetime_open,
                'dateTimeClose' => $row->datetime_close,
                'townId' => $row->town_id,
                'pointId' => $row->point_id,
                'roleId' => $row->role_id
            ];
            if ($row->point_id && empty($errorReport['pointList'][$row->point_id])) {
                $errorReport['pointList'][$row->point_id] = [
                    'name' => ''
                ];
            }
            if ($row->town_id && empty($errorReport['townList'][$row->town_id])) {
                $errorReport['townList'][$row->town_id] = [
                    'name' => ''
                ];
            }
            if (empty($errorReport['roleList'][$row->role_id])) {
                $errorReport['roleList'][$row->role_id] = [
                    'name' => ''
                ];
            }
        };

        foreach ($workdayRows as $row) {
            if (is_null($row->datetime_close)) {
                $registerError('Нет записи о закрытии смены', $row);
                continue;
            }

            if (empty($employees[$row->employee_id])) {
                $employees[$row->employee_id] = (new CalcEmployeeSalary($row->employee_id))
                    ->setEmployeeName($row->employee_name);
            }
            try {
                $employees[$row->employee_id]->addWorkday(
                    $row->datetime_round_open,
                    $row->datetime_round_close,
                    $row->datetime_open,
                    $row->datetime_close,
                    $row->role_id,
                    $row->point_id,
                    $row->town_id,
                    (bool)$row->is_holiday
                );
            } catch (\Exception $e) {
                $registerError($e->getMessage(), $row);
            }
        }

        foreach ($errorReport['workdayErrors'] as $employeeId => $error) {
            unset($employees[$employeeId]);
        }

        if (!empty($errorReport['pointList'])) {
            $rows = DB::select('SELECT id, name FROM points WHERE id in (:ids)', ['ids' => join(',', array_keys($errorReport['pointList']))]);
            foreach ($rows as $row) {
                $errorReport['pointList'][$row->id]['name'] = $row->name;
            }
        }
        if (!empty($errorReport['roleList'])) {
            $rows = DB::select('SELECT id, name, guard_name FROM roles WHERE id in (:ids)', ['ids' => join(',', array_keys($errorReport['roleList']))]);
            foreach ($rows as $row) {
                $errorReport['roleList'][$row->id] = [
                    'name' => $row->name,
                    'guardName' => $row->guard_name
                ];
            }
        }
        if (!empty($errorReport['townList'])) {
            $rows = DB::select('SELECT id, name FROM towns WHERE id in (:ids)', ['ids' => join(',', array_keys($errorReport['townList']))]);
            foreach ($rows as $row) {
                $errorReport['townList'][$row->id]['name'] = $row->name;
            }
        }

        return [
            'employees' => array_values($employees),
            'errorReport' => $errorReport
        ];
    }
}
