<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class CalcEmployeeSalariesHandler
{
    public function handle(CalcEmployeeSalariesCommand $command): array
    {
        if (! $command->getYear() || ! $command->getMonth()) {
            throw new \Exception('Даты заданы неверно', Response::HTTP_BAD_REQUEST);
        }

        $dateFrom = new \DateTime("{$command->getYear()}-{$command->getMonth()}-01 00:00:00");
        $endDayOfMonth = $dateFrom->format('t');
        $dateTo = new \DateTime("{$command->getYear()}-{$command->getMonth()}-{$endDayOfMonth} 23:59:59");

        $dateFromString = $dateFrom->format('Y-m-d H:i:s');
        $dateToString = $dateTo->format('Y-m-d H:i:s');

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

        $roleIds = implode(',', CalcEmployeeSalaryConstant::ROLE_IDS);

        $workdaysSql = <<<SQL_END
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
       h.id                                                                as 'is_holiday'
FROM workdays w_open
         INNER JOIN users u on u.id = w_open.employee_id
         LEFT JOIN points p on p.id = w_open.point_id
         INNER JOIN model_has_roles mhr on mhr.model_id = w_open.employee_id and mhr.role_id IN ({$roleIds})
         LEFT JOIN workdays w_close on w_open.id = w_close.open_workday_id and w_close.admitted = 1 AND w_close.deleted_at is null
         LEFT JOIN holidays h on h.date = STR_TO_DATE(DATE_FORMAT(w_open.date, '%Y-%m-%d'), '%Y-%m-%d')
WHERE w_open.admitted = 1
  AND w_open.type_anketa = 1
  AND w_open.date >= :date_from
  AND w_open.date <= :date_to
  AND w_open.deleted_at is null
SQL_END;

        $args = [
            'date_from' => $dateFromString,
            'date_to' => $dateToString,
        ];

        if ($town = $command->getTown()) {
            $workdaysSql .= ' AND p.pv_id = :town';
            $args['town'] = $town;
        }
        if ($role = $command->getRole()) {
            $workdaysSql .= ' AND mhr.role_id = :role';
            $args['role'] = $role;
        }
        if (! empty($pointList = $command->getPointList())) {
            $pointIds = implode(',', array_map('intval', $pointList));
            $workdaysSql .= " AND p.id IN ({$pointIds})";
        }
        if (! empty($employeeList = $command->getEmployeeList())) {
            $employeeIds = implode(',', array_map('intval', $employeeList));
            $workdaysSql .= " AND w_open.employee_id IN ({$employeeIds})";
        }

        $workdayRows = DB::select($workdaysSql, $args);

        $employees = [];
        $errorReport = [];

        $registerError = static function (string $message, $row) use (&$errorReport) {
            if (empty($errorReport[$row->employee_id])) {
                $errorReport[$row->employee_id] = [];
            }

            $errorReport[$row->employee_id][] = [
                'employeeId' => $row->employee_id,
                'hour' => (new \DateTime($row->datetime_round_open))->format('H'),
                'errorMessage' => $message,
                'dateTimeOpen' => $row->datetime_open,
                'dateTimeClose' => $row->datetime_close,
                'townId' => $row->town_id,
                'pointId' => $row->point_id,
                'roleId' => $row->role_id,
            ];
        };

        foreach ($workdayRows as $row) {
            if (is_null($row->datetime_close)) {
                $registerError('Нет записи о закрытии смены', $row);
                continue;
            }

            if (empty($employees[$row->employee_id])) {
                $employees[$row->employee_id] = (new CalcEmployeeSalary($row->employee_id));
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
                    (bool) $row->is_holiday
                );
            } catch (\Exception $e) {
                $registerError($e->getMessage(), $row);
            }
        }

        foreach ($errorReport as $employeeId => $error) {
            unset($employees[$employeeId]);
        }

        return [
            'employees' => array_values($employees),
            'errorReport' => $errorReport,
        ];
    }
}
