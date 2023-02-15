<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Company;
use App\Driver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Expression;
use SebastianBergmann\Environment\Console;

class BddDriversinstructionsSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bdd:instructions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $driversCompanies = Company::query()
            ->with('drivers')
            ->orWhereRaw(new Expression("FIND_IN_SET('9', companies.products_id)"))
            ->orWhereRaw(new Expression("FIND_IN_SET('25', companies.products_id)"))
            ->orWhereRaw(new Expression("FIND_IN_SET('20', companies.products_id)"))
            ->orWhereRaw(new Expression("FIND_IN_SET('24', companies.products_id)"))
            ->orWhereRaw(new Expression("FIND_IN_SET('6', companies.products_id)"))
            ->cursor()
        ;

        foreach ($driversCompanies as $company) {
            /** @var Driver $driver */
            foreach ($company->drivers()->with('inspections_medic')->cursor() as $driver) {
                $ankets = $driver->inspections_medic()
                    ->whereBetween('anketas.date', [
                        \DateTime::createFromFormat('Y-m-d', '2022-07-01'),
                        \DateTime::createFromFormat('Y-m-d', '2023-02-01')]
                    )->get();

                dump($driver->fio);
                dump('Количество мед. осмотров c 2022-07-01 по 2023-02-01 ' . $ankets->count());
                sleep(3);
                $new_ankets_cnt = 0;
                if ($ankets->count() > 0) {
                    /** @var Anketa $anket */
                    foreach ($ankets as $anket) {
                        $date = Carbon::parse($anket->date);
                        $new_ankets_cnt++;
                        $bddDate = Carbon::create($date->year, $date->month, 10, 6);
                        $driver->inspections_bdd()->create([
                            'type_anketa' => 'bdd',
                            'pv_id' => $anket->pv_id,
                            'date' => $bddDate,
                            'type_briefing' => 'Специальный',
                            'signature' => 'Подписано простой ЭЦП',
                            'user_eds' => $anket->user_eds,
                            'driver_id' => $driver->id,
                            'driver_fio' => $driver->fio,
                            'company_name' => $driver->company->name,
                        ]);
                        $this->info("Добавлен инструктаж для $driver->fio : $driver->hash_id" );
                    }
                    $this->info("Добавлено анкет $new_ankets_cnt");
                }
            }
        }

        return 0;

    }
}
