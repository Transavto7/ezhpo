<?php

namespace App\Jobs;

use App\Enums\ReportStatus;
use App\Models\Report;
use DateTimeImmutable;
use Exception;
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
        try {
            // TODO: добавить реализацию формирования отчета
            $reportData = [];

            $now = new DateTimeImmutable();
            $reportFilename = 'report_'.$now->format('Y-m-d_H:i:s').'.json';

            if (!Storage::disk('report')->put($reportFilename, json_encode($reportData))) {
                throw new Exception('Could not write report file.');
            }

            $this->report->update([
                'status' => ReportStatus::READY,
                'path' => $reportFilename,
            ]);
        } catch (Exception $exception) {
            $this->report->update([
                'status' => ReportStatus::error(),
            ]);
        }
    }
}
