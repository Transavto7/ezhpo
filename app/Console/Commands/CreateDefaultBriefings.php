<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Instr;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        $companiesWithAutoBriefing = Company::where("required_type_briefing", true)->select('name', 'id', 'hash_id')->get();
        /** @var $drivers Driver Данные водителей, которым нужно прописать инструктаж */
        $drivers = Driver::select(["hash_id", "fio", "gender", "year_birthday"])
            ->whereIn("company_id", $companiesWithAutoBriefing->pluck("id"))->get();

        $rmsEngineerIds = [];
        DB::table("model_has_roles")
          ->select("model_id")
          ->where("role_id", 7)
          ->get()
          ->map(function ($container) use (&$rmsEngineerIds) {
              $rmsEngineerIds[] = $container->model_id;
          });
        $rmsEngineer = User::whereIn("id", $rmsEngineerIds)->inRandomOrder()->first();

        $drivers->map(function ($driver) use ($rmsEngineer, $companiesWithAutoBriefing, &$entersInto) {
            $company = $companiesWithAutoBriefing->where('hash_id', $driver->company_id);
            Anketa::create([
                               "type_anketa" => "bdd",
                               "user_id"     => $rmsEngineer->id,
                               "user_name"   => $rmsEngineer->name,
                               "driver_id"   => $driver->hash_id,
                               "driver_fio"  => $driver->fio,
                               "driver_gender" => $driver->gender,
                               "driver_year_birthday" => $driver->year_birthday,
                               "complaint" => "Нет",
                               "signature" => "Подписано простой ЭЦП",
                               "condition_visible_sliz" => "Без особенностей",
                               "condition_koj_pokr" => "Без особенностей",
                               "date" => Carbon::now(),
                               "type_view" => "Предрейсовый",
                               "company_id" => $company->hash_id,
                               "company_name" => $company->name,
//                               "briefing_name" => $defaultBriefing
            ]);
            $entersInto++;
        });

        $this->info("Created $entersInto records.");
    }
}
