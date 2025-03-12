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

        $rows = DB::select("
SELECT t.datetime_open,
       t.datetime_close,
       t.employee_id,
       t.role_id,
       t.is_holiday,
       t.employee_name,
       (CASE
            WHEN t_point.id is not null THEN t_point.date
            WHEN t_town.id is not null THEN t_town.date
            ELSE t_base.date END)       as 'range',
       (CASE
            WHEN t_point.id is not null THEN t_point.price_hour
            WHEN t_town.id is not null THEN t_town.price_hour
            ELSE t_base.price_hour END) as 'price_hour',
       (CASE
            WHEN t_point.id is not null THEN t_point.price_cfg
            WHEN t_town.id is not null THEN t_town.price_cfg
            ELSE t_base.price_cfg END)  as 'price_cfg'
FROM (SELECT STR_TO_DATE(DATE_FORMAT(w_open.date, '%Y-%m-%d %H'), '%Y-%m-%d %H') as 'datetime_round_open',
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
        AND w_open.date <= '{$dateToString}') t
         LEFT JOIN tarifs t_base
                   on t_base.point_id is null and t_base.town_id is null and t_base.role_id = t.role_id and
                      t_base.date BETWEEN t.datetime_round_open AND t.datetime_round_close
         LEFT JOIN tarifs t_town on t_town.town_id = t.town_id and t_town.role_id = t.role_id and
                                    t_town.date BETWEEN t.datetime_round_open AND t.datetime_round_close
         LEFT JOIN tarifs t_point on t_point.point_id = t.point_id and t_point.role_id = t.role_id and
                                     t_point.date BETWEEN t.datetime_round_open AND t.datetime_round_close
HAVING price_hour is not null;");

        $result = [];

        foreach ($rows as $row) {
            if (empty($result[$row->employee_id])) {
                $result[$row->employee_id] = (new CalcEmployeeSalariesResponse($row->employee_id))
                    ->setEmployeeName($row->employee_name);
            }

            $result[$row->employee_id]->addSalary(
                $row->datetime_open,
                $row->datetime_close,
                (bool)$row->is_holiday,
                $row->range,
                $row->price_hour,
                $row->price_cfg,
                $row->role_id
            );
        }

        return array_values($result);
    }
}
