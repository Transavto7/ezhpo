<?php

namespace App\Console\Commands\Companies;

use App\Company;
use App\Models\Forms\Form;
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
        $companiesWithInspection = Form::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ])
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
