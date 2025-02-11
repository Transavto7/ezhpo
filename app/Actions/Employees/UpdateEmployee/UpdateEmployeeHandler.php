<?php

namespace App\Actions\Employees\UpdateEmployee;

use App\Actions\User\UpdateUser\UpdateUserCommand;
use App\Employee;
use Illuminate\Contracts\Bus\Dispatcher;

final class UpdateEmployeeHandler
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(UpdateEmployeeCommand $command)
    {
        $employee = Employee::withTrashed()->find($command->getId());
        if (!$employee) {
            throw new \DomainException('Сотрудник не найден');
        }

        $user = $employee->user()->withTrashed()->first();
        if (!$user) {
            throw new \DomainException('Пользователь не найден');
        }

        $employee->update([
            'name' => $command->getName(),
            'blocked' => $command->getBlocked(),
            'pv_id' => $command->getPvId(),
            'eds' => $command->getEds(),
            'validity_eds_start' => $command->getValidityEdsStart(),
            'validity_eds_end' => $command->getValidityEdsEnd(),
        ]);

        $this->dispatcher->dispatch(new UpdateUserCommand(
            $user,
            $command->getLogin(),
            $command->getEmail(),
            $command->getPassword(),
            $command->getTimezone(),
            $command->getRoles(),
            $command->getPermissions()
        ));

        // todo: points должны быть у employee
        $user->points()->sync($command->getPvs());
    }
}