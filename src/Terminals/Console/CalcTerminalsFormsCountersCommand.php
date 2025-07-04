<?php

namespace Src\Terminals\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Src\Terminals\Commands\CalcTerminalsCounters\CalcCurrentMonthAmountHandler;
use Src\Terminals\Commands\CalcTerminalsCounters\CalcLastMonthAmountHandler;
use Throwable;

class CalcTerminalsFormsCountersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'terminals:calc-forms-counters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Подсчет количества осмотров у каждого терминала';

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
    public function handle(CalcLastMonthAmountHandler $calcLastMonthAmountHandler, CalcCurrentMonthAmountHandler $calcCurrentMonthAmountHandler)
    {
        try {
            DB::beginTransaction();

            $this->info("Терминалов с осмотрами в предыдущем месяце: " . $calcLastMonthAmountHandler->handle());

            $this->info("Терминалов с осмотрами в текущем месяце: " . $calcCurrentMonthAmountHandler->handle());

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->error("Ошибка обновления счетчиков количества осмотров у терминалов! {$exception->getMessage()}");
        }
    }
}
