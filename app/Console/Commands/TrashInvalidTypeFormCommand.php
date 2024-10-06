<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TrashInvalidTypeFormCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:trash-invalid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление осмотров с невалидным типом';

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
        $counter = DB::table('anketas')
            ->where('type_anketa', 'pak')
            ->orWhere('type_anketa', 'vid_pl')
            ->orWhere('type_anketa', 'Dop')
            ->delete();

        $this->info("Удалено записей с невалидным типом: $counter");
    }
}
