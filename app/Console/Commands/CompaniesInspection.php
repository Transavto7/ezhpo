<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompaniesInspection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:inspect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Conducts inspections of companies';

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
        $companiesWithInspection = Anketa::where(function ($q) {
                $q->where(function ($q) {
                    $q->whereNotNull('anketas.date')
                        ->whereBetween('anketas.date', [
                            Carbon::now()->subMonth()->startOfDay(),
                            Carbon::now()->subMonth()->endOfDay(),
                        ]);
                })
                    ->orWhere(function ($q) {
                        $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                            Carbon::now()->subMonth()->format('Y-m'),
                            Carbon::now()->subMonth()->format('Y-m'),
                        ]);
                    });
            })
            ->whereNotNull('company_id')
            ->get(['company_id'])
            ->pluck('company_id')
            ->unique();

        Company::whereIn('hash_id', $companiesWithInspection)
            ->update(['has_actived_prev_month' => 'Да']);

        Company::whereNotIn('hash_id', $companiesWithInspection)
            ->update(['has_actived_prev_month' => 'Нет']);
    }
}
