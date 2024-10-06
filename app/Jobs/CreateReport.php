<?php

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Storage;

class CreateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // TODO: добавить реализацию формирования отчета

        $path = Storage::disk('report')->path('report.json');

        file_put_contents($path, json_encode($this->report));

        $this->report->update([
            'status' => ReportStatus::READY,
            'path' => 'report.json',
        ]);
    }
}
