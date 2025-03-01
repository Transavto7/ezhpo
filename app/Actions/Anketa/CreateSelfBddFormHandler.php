<?php

namespace App\Actions\Anketa;

use App\User;
use Illuminate\Database\Eloquent\Builder;

class CreateSelfBddFormHandler extends CreateBddFormHandler
{
    protected function addUserInfo()
    {
        /** @var User $bddUser */
        $bddUser = User::with(['roles'])
            ->whereHas('roles', function (Builder $queryBuilder) {
                return $queryBuilder->where('id', 7);
            })
            ->get()
            ->random();

        $this->data['user_id'] = $bddUser->id;
        $this->data['user_eds'] = $bddUser->eds;
        $this->data['user_validity_eds_start'] = $bddUser->validity_eds_start;
        $this->data['user_validity_eds_end'] = $bddUser->validity_eds_end;
    }
}
