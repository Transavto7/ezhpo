<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Instr;
use App\Point;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CreateDefaultBriefings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:briefings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create RMS briefing with based type';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $entersInto = 0;
        /** @var $companiesWithAutoBriefing array Массив с hash ID компаний, где требуется базовый инструктаж */
        $companiesWithAutoBriefing = Company::where("required_type_briefing", true)->select('name', 'id', 'hash_id', 'pv_id')->get();
        /** @var $drivers Driver Данные водителей, которым нужно прописать инструктаж */
        $drivers = Driver::select(["hash_id", "fio", "gender", "year_birthday", 'company_id'])
            ->whereIn("company_id", $companiesWithAutoBriefing->pluck("id"))->get();
        $briefing = Instr::where('is_default', true)->where('type_briefing', 'Специальный')->first();

        $bddUser = User::with(['roles'])->whereHas('roles', function (Builder $queryBuilder) {
            return $queryBuilder->where('id', 7);
        })->get()->random();

        $drivers->map(function ($driver) use ($bddUser, $companiesWithAutoBriefing, &$entersInto, $briefing) {
            $company = $companiesWithAutoBriefing->where('id', $driver->company_id)->first();
            $point = Point::find($company->pv_id);

            Anketa::create([
                               "type_anketa" => "bdd",
                               "user_id"     => $bddUser->id,
                               "user_name"   => $bddUser->name,
                               'pv_id'       => $point->name,
                               'user_eds'    => $bddUser->eds,
                               "driver_id"   => $driver->hash_id,
                               "driver_fio"  => $driver->fio,
                               "driver_gender" => $driver->gender,
                               "driver_year_birthday" => $driver->year_birthday,
                               "complaint" => "Нет",
                               "type_briefing" => 'Специальный',
                               "signature" => "Подписано простой ЭЦП",
                               "condition_visible_sliz" => "Без особенностей",
                               "condition_koj_pokr" => "Без особенностей",
                               "date" => Carbon::now(),
                               "type_view" => "Предрейсовый",
                               "company_id" => $company->hash_id,
                               "company_name" => $company->name,
                               'point_id' => $point->id,
                               "briefing_name" => $briefing->name ?? ''
            ]);
            $entersInto++;
        });

        $this->info("Created $entersInto records.");
    }
}
