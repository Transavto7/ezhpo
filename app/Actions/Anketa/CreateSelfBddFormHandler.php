<?php

namespace App\Actions\Anketa;

use App\Enums\UserRoleEnum;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class CreateSelfBddFormHandler extends CreateBddFormHandler
{
    protected function addUserInfo()
    {
        /** @var User $bddUser */
        $bddUser = User::with(['roles'])
            ->whereHas('roles', function (Builder $queryBuilder) {
                return $queryBuilder->where('id', UserRoleEnum::ENGINEER_BDD);
            })
            ->get()
            ->random();

        $employee = $bddUser->relatedEmployee;

        $this->data['user_id'] = $bddUser->id;
        $this->data['user_eds'] = $employee->eds;
        $this->data['user_validity_eds_start'] = $employee->validity_eds_start;
        $this->data['user_validity_eds_end'] = $employee->validity_eds_end;
    }
}
