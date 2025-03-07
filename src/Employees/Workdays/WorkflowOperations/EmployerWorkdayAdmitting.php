<?php

namespace Src\Employees\Workdays\WorkflowOperations;

use Src\Employees\Workdays\Eloquent\Workday;

final class EmployerWorkdayAdmitting
{
    private $t_people_test_status;
    private $pressure_test_status;
    private $pulse_test_status;
    private $alcometer_test_status;
    private $narko_test_status;

    private function __construct()
    {
    }

    public static function fromWorkday(Workday $workday)
    {
        $item = new self();

        $item->t_people_test_status = $workday->t_people_test_status;
        $item->pressure_test_status = $workday->pressure_test_status;
        $item->pulse_test_status = $workday->pulse_test_status;
        $item->alcometer_test_status = $workday->alcometer_test_status;
        $item->narko_test_status = $workday->narko_test_status;

        return $item;
    }

    public function isAllowedWork(): bool
    {
        return (is_null($this->t_people_test_status) || $this->t_people_test_status) &&
            (is_null($this->pressure_test_status) || $this->pressure_test_status) &&
            (is_null($this->pulse_test_status) || $this->pulse_test_status) &&
            (is_null($this->alcometer_test_status) || $this->alcometer_test_status) &&
            (is_null($this->narko_test_status) || $this->narko_test_status);
    }
}
