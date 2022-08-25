<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Exports\AnketasExport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class exportAnketas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:anketas {--type=} {--from=} {--to=} {--delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export anketas by type';

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

        $anketas = $request->get();

        $this->info('Exporting anketas rows ' . $type . ' from ' . $from->format('d.m.Y i:s') .
        ' to ' . $to->format('d.m.Y i:s') . ' | ' . $anketas->count() . ' rows');

        Excel::store(new AnketasExport($anketas, Anketa::$fieldsKeys[$type]),
            'exports/anketas/' . $type . '.xlsx');

        if ($this->option('delete')) {
            $this->info('deleting rows...');
            $request->delete();
        }

        $this->info('finish!');
    }
}
