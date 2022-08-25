<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Exports\AnketasExport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class DeleteAnketas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:anketas {--type=} {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete anketas by type';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', 0);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->option('type');

        if (!$type) {
            $this->error('Enter type anketas');
            return;
        }

        $from = $this->option('from');
        if ($from) {
            $from = Carbon::parse($from)->endOfDay();
        } else {
            $from = Carbon::now()->startOfYear();
        }

        $to = $this->option('to');
        if ($to) {
            $to = Carbon::parse($to)->endOfDay();
        } else {
            $to = Carbon::now();
        }



        $request = Anketa::where('type_anketa', 'like', $type)->whereBetween('created_at', [
            $from,
            $to
        ]);

        $this->info('Deleting anketas rows ' . $type . ' from ' . $from->format('d.m.Y i:s') .
            ' to ' . $to->format('d.m.Y i:s') . ' | ' . $request->count() . ' rows');

        $request->delete();

        $this->info('finish!');
    }
}
