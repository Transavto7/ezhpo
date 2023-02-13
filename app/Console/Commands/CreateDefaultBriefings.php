<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Instr;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

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
        /** @var $companiesWithAutoBriefing array Массив с hash ID компаний, где требуется базовый инструктаж */
        $companiesWithAutoBriefing = Company::where("required_type_briefing", true)->pluck("hash_id", "name");
        /** @var $defaultBriefing int Hash ID базового инструктажа */
        $defaultBriefing = Instr::where("is_default", true)->pluck("hash_id");
        /** @var $drivers Driver Данные водителей, которым нужно прописать инструктаж */
        $drivers = Driver::select(["hash_id", "fio", "gender", "year_birthday"])->whereIn("company_id", $companiesWithAutoBriefing)->get();
        $user = Auth::user();

        $drivers->map(function ($driver) use ($user, $companiesWithAutoBriefing) {
            Anketa::create([
                               "type_anketa" => "bdd",
                               "user_id"     => $user->id,
                               "user_name"   => $user->name,
                               "driver_id"   => $driver->hash_id,
                               "driver_fio"  => $driver->fio,
                               "driver_gender" => $driver->gender,
                               "driver_year_birthday" => $driver->year_birthday,
                               "complaint" => "Нет",
                               "condition_visible_sliz" => "Без особенностей",
                               "condition_koj_pokr" => "Без особенностей",
                               "date" => Carbon::now(),
                               "type_view" => "Предрейсовый",
                               "company_id" => $driver->company_id,
                               "company_name" => $companiesWithAutoBriefing->where("hash_id", $driver->company_id)->pluck("name")
                           ]);
        });
    }
}
