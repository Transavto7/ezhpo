<?php

namespace App\Actions\Employees\DeleteEmployee;

use App\Actions\User\DeleteUser\DeleteUserCommand;
use App\Employee;
use Illuminate\Contracts\Bus\Dispatcher;

final class DeleteEmployeeHandler
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

    public function handle(DeleteEmployeeCommand $command)
    {
        $employee = Employee::find($command->getId());
        if (!$employee) {
            throw new \DomainException('Сотрудник не найден');
        }

        $user = $employee->user;
        if (!$user) {
            throw new \DomainException('Пользователь не найден');
        }

        $employee->delete();

        $this->dispatcher->dispatch(new DeleteUserCommand($user));
    }
}