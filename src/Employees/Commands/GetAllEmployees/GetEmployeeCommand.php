<?php
declare(strict_types=1);

namespace Src\Employees\Commands\GetAllEmployees;

final class GetEmployeeCommand
{
    private $filterHashId;

    /**
     * @return self
     */
    public function getFilterHashId()
    {
        return $this->filterHashId;
    }

    /**
     * @param int $filterHashId
     * @return GetEmployeeCommand
     */
    public function setFilterHashId(int $filterHashId): self
    {
        $this->filterHashId = $filterHashId;

        return $this;
    }
}
