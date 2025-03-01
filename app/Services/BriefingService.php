<?php
declare(strict_types=1);

namespace App\Services;

use App\Company;
use App\Driver;
use App\Instr;
use App\Models\Forms\BddForm;
use App\Models\Forms\Form;
use App\User;
use Carbon\Carbon;

final class BriefingService
{
    public static function createFirstBriefingForDriver(Driver $driver, ?Company $company = null): Form
    {
        if ($company === null) {
            $company = $driver->company;
        }
        $point = $company->point;

        /** @var Instr|null $briefing */
        $briefing = Instr::query()
            ->where('is_default', true)
            ->where('type_briefing', 'Вводный')
            ->first();

        /** @var User $bddUser */
        $bddUser = User::query()
            ->with(['roles'])
            ->whereHas('roles', function ($queryBuilder) {
                return $queryBuilder->where('id', 7);
            })
            ->get()
            ->random();

        $form = Form::create([
            "driver_id" => $driver->hash_id,
            'point_id' => $point->id,
            "company_id" => $company->hash_id,
            "type_anketa" => "bdd",
            "date" => Carbon::now(),
            "user_id" => $bddUser->id,
            'user_eds' => $bddUser->eds,
            'user_validity_eds_start' => $bddUser->validity_eds_start,
            'user_validity_eds_end' => $bddUser->validity_eds_start,
        ]);

        BddForm::create([
            'forms_uuid' => $form->uuid,
            "type_briefing" => 'Вводный',
            "signature" => "Подписано простой электронной подписью (ПЭП)",
            "briefing_name" => $briefing->name ?? '',
        ]);

        return $form;
    }
}
