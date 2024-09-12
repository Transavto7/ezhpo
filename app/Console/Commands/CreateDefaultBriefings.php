<?php

namespace App\Console\Commands;

use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Instr;
use App\Models\Forms\BddForm;
use App\Models\Forms\Form;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

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
        $companiesWithAutoBriefing = Company::query()
            ->select(['name', 'id', 'hash_id', 'pv_id'])
            ->where("required_type_briefing", true)
            ->get();

        if ($companiesWithAutoBriefing->count() === 0) {
            return;
        }

        $briefing = Instr::query()
            ->where('is_default', true)
            ->where('type_briefing', 'Специальный')
            ->first();

        if ($briefing === null) {
            return;
        }

        $briefingName = $briefing->name;

        $bddUser = User::query()
            ->with(['roles'])
            ->whereHas('roles', function (Builder $queryBuilder) {
                return $queryBuilder->where('id', 7);
            })
            ->get()
            ->random();

        if ($bddUser === null) {
            return;
        }

        $drivers = Form::query()
            ->select(['driver_id'])
            ->leftJoin('companies', 'companies.id', '=', 'forms.company_id')
            ->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()
            ])
            ->where('companies.required_type_briefing', true)
            ->get()
            ->pluck('driver_id')
            ->unique();

        if ($drivers->count() === 0) {
            return;
        }

        $drivers = Driver::query()
            ->select([
                "drivers.hash_id",
                'companies.hash_id as company_id',
                'companies.pv_id as point_id'
            ])
            ->leftJoin('companies', 'companies.id', '=', 'drivers.company_id')
            ->whereIn('hash_id', $drivers)
            ->get();

        $drivers->each(function (Driver $driver) use ($bddUser, $briefingName) {
            $form = Form::create([
                "driver_id" => $driver->hash_id,
                'point_id' => $driver->point_id,
                "company_id" => $driver->company_id,
                "type_anketa" => FormTypeEnum::BDD,
                "user_id" => $bddUser->id,
                'user_eds' => $bddUser->eds,
                'user_validity_eds_start' => $bddUser->validity_eds_start,
                'user_validity_eds_end' => $bddUser->validity_eds_end,
                "date" => Carbon::now(),
            ]);

            BddForm::create([
                'forms_uuid' => $form->uuid,
                "type_briefing" => 'Специальный',
                "signature" => "Подписано простой электронной подписью (ПЭП)",
                "briefing_name" => $briefingName
            ]);
        });

        $count = $drivers->count();
        $this->info("Created $count records.");
    }
}
