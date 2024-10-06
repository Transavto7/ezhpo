<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TrashOldFormCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:trash-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление старых осмотров';

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
        $deleteAfterMonth = 2;

        $counter = DB::table('anketas')
            ->where('in_cart', 1)
            ->where(function (Builder $query) use ($deleteAfterMonth) {
                $query
                    ->where(function (Builder $subQuery) use ($deleteAfterMonth) {
                        $subQuery
                            ->whereNull('deleted_at')
                            ->whereRaw("created_at <= (NOW() + INTERVAL -$deleteAfterMonth MONTH)");
                    })
                    ->orWhere(function (Builder $subQuery) use ($deleteAfterMonth) {
                        $subQuery
                            ->whereNotNull('deleted_at')
                            ->whereRaw("deleted_at <= (NOW() + INTERVAL -$deleteAfterMonth MONTH)");
                    });
            })
            ->delete();

        $this->info("Удалено записей старше $deleteAfterMonth мес. в корзине: $counter");
    }
}
