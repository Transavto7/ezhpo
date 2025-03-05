<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

final class CalcEmployeeSalaryConstant
{
    public const MONTH_LIST = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];

    public const START_YEAR = 2025;

    public const ROLE_IDS = [1, 2];

    /**
     * @const ID ролей в приоритете (если у сотрудника есть обе роли, то в ЗП будет использоваться та, что приоритетнее)
     * */
    public const PRIORITY_ROLE = [2, 1];
}
