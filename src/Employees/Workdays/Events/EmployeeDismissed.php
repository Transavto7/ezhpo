<?php

namespace Src\Employees\Workdays\Events;

use Illuminate\Queue\SerializesModels;
use Src\Employees\Workdays\Eloquent\Workday;

class EmployeeDismissed
{
    use SerializesModels;

    /**
     * @var Workday
     */
    private $workday;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Workday $workday)
    {
        $this->workday = $workday;
    }

    public function getWorkday(): Workday
    {
        return $this->workday;
    }
}
