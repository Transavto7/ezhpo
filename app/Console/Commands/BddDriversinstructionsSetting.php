<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Company;
use App\Driver;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

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
            ->cursor();

        /** @var User $bddUser */
        $bddUser = User::with(['roles'])->whereHas('roles', function (Builder $queryBuilder) {
            return $queryBuilder->where('id', 7);
        })->first();

        foreach ($driversCompanies as $company) {
            /** @var Driver $driver */
            foreach ($company->drivers()->with('inspections_medic')->cursor() as $driver) {
                $anketsByMonth = $driver->inspections_medic()
                    ->selectRaw(
                        new Expression("
                            count(*) as count,
                            DATE_FORMAT(`date`,'%Y-%m') dateMonth,
                            id
                        ")
                    )
                    ->whereBetween('anketas.date', [
                            \DateTime::createFromFormat('Y-m-d', '2022-07-01'),
                            \DateTime::createFromFormat('Y-m-d', '2023-02-01')]
                    )
                    ->groupBy([DB::raw("dateMonth")])

                ;

                foreach ($anketsByMonth->get()->pluck('count', 'dateMonth')->toArray() as $date => $count) {
                    $date = Carbon::parse($date);
                    $bddDate = Carbon::create($date->year, $date->month, 10, 6);
                    $model = $driver->inspections_bdd()->create([
                        'type_anketa' => 'bdd',
                        'pv_id' => $bddUser->pv_id,
                        'date' => $bddDate,
                        'type_briefing' => 'Специальный',
                        'signature' => 'Подписано простой ЭЦП',
                        'user_eds' => $bddUser->eds,
                        'driver_id' => $driver->id,
                        'driver_fio' => $driver->fio,
                        'company_name' => $driver->company->name,
                        'user_name' => $bddUser->name,
                        'user_id' => $bddUser->id
                    ]);

                    dump($model->toArray());

                }
            }
        }


        return 0;

    }
}
