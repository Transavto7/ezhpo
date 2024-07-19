<?php
declare(strict_types=1);

namespace App\Services;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Instr;
use App\User;
use Carbon\Carbon;

final class BriefingService
{
    public static function createFirstBriefingForDriver(Driver $driver, ?Company $company = null): Anketa
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

        /** @var Anketa $anketa */
        $anketa = Anketa::query()->create([
            "type_anketa" => "bdd",
            "complaint" => "Нет",
            "type_briefing" => 'Вводный',
            "signature" => "Подписано простой электронной подписью (ПЭП)",
            "condition_visible_sliz" => "Без особенностей",
            "condition_koj_pokr" => "Без особенностей",
            "date" => Carbon::now(),
            "type_view" => "Предрейсовый",

            "user_id" => $bddUser->id,
            "user_name" => $bddUser->name,
            'user_eds' => $bddUser->eds,
            'user_validity_eds_start' => $bddUser->validity_eds_start,
            'user_validity_eds_end' => $bddUser->validity_eds_start,

            "driver_id" => $driver->hash_id,
            "driver_fio" => $driver->fio,
            "driver_gender" => $driver->gender,
            "driver_year_birthday" => $driver->year_birthday,

            'pv_id' => $point->name ?? null,
            'point_id' => $point->id ?? null,

            "company_id" => $company->hash_id,
            "company_name" => $company->name,
            "briefing_name" => $briefing->name ?? '',
        ]);

        return $anketa;
    }
}
