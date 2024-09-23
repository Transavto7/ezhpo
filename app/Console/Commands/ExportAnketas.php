<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Exports\AnketasExport;
use App\Models\Forms\Form;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExportAnketas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:anketas {--type=} {--from=} {--to=}';

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
        ini_set('max_execution_time', 0);

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

        $query = Form::query()
            ->where('type_anketa', $type)
            ->leftJoin()
            ->whereBetween('created_at', [
                $from,
                $to
            ]);

        $forms = $query->get();

        $message = sprintf(
            'Exporting anketas rows %s from %s to %s | %s rows',
            $type,
            $from->format('d.m.Y i:s'),
            $to->format('d.m.Y i:s'),
            $query->count()
        );
        $this->info($message);

        $fileName = "exports/anketas/$type.xlsx";
        Excel::store(new AnketasExport($forms, Anketa::$fieldsKeys[$type]), $fileName);

        $this->info('finish!');
    }
}
