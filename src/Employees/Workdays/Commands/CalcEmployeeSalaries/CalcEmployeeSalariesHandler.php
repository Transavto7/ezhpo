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
       p.pv_id                                                             as 'town_id',
       h.id                                                                as 'is_holiday',
       u.name                                                              as 'employee_name'
FROM workdays w_open
         INNER JOIN users u on u.id = w_open.employee_id
         LEFT JOIN points p on p.pv_id = u.pv_id
         INNER JOIN model_has_roles mhr on mhr.model_id = w_open.employee_id and mhr.role_id in (1, 2)
         INNER JOIN workdays w_close
                    on w_open.employee_id = w_close.employee_id and w_open.id = w_close.open_workday_id and
                       w_close.admitted = 1
         LEFT JOIN holidays h on h.date = STR_TO_DATE(DATE_FORMAT(w_open.date, '%Y-%m-%d'), '%Y-%m-%d')
WHERE w_open.admitted = 1
  AND w_open.type_anketa = 1
  AND w_open.date >= '{$dateFromString}'
  AND w_open.date <= '{$dateToString}'");

        $result = [];

        foreach ($workdayRows as $row) {
            if (empty($result[$row->employee_id])) {
                $result[$row->employee_id] = (new CalcEmployeeSalariesResponse($row->employee_id))
                    ->setEmployeeName($row->employee_name);
            }
            $result[$row->employee_id]->addWorkday(
                $row->datetime_round_open,
                $row->datetime_round_close,
                $row->datetime_open,
                $row->datetime_close,
                $row->role_id,
                $row->point_id,
                $row->town_id,
                $row->is_holiday
            );
        }

        return array_values($result);
    }
}
