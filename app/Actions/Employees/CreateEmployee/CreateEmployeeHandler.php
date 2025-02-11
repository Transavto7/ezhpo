<?php

namespace App\Actions\Employees\CreateEmployee;

use App\Actions\User\CreateUser\CreateUserCommand;
use App\Employee;
use App\Enums\UserEntityType;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Hash;

final class CreateEmployeeHandler
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

    public function handle(CreateEmployeeCommand $command)
    {
        $employee = Employee::create([
            'name' => $command->getName(),
            'blocked' => $command->getBlocked(),
            'pv_id' => $command->getPvId(),
            'eds' => $command->getEds(),
            'validity_eds_start' => $command->getValidityEdsStart(),
            'validity_eds_end' => $command->getValidityEdsEnd(),
        ]);

        $apiToken = Hash::make(date('H:i:s') . sha1($command->getPassword()));

        $user = $this->dispatcher->dispatch(new CreateUserCommand(
            $employee->id,
            UserEntityType::employee(),
            $command->getLogin(),
            $command->getEmail(),
            $command->getPassword(),
            $command->getTimezone(),
            $apiToken,
            null,
            0,
            $command->getRoles(),
            $command->getPermissions()
        ));

        // todo: points должны быть у employee
        $user->points()->sync($command->getPvs());
    }
}