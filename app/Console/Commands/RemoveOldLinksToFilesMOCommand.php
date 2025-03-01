<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveOldLinksToFilesMOCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mo-file-links:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old links to files from MO';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $runCommand = config('forms.deleting-mo-files');

        if (! $runCommand) {
            return;
        }

        $dayCount = config('forms.days-before-deleting-mo-files');
        $chunk = config('forms.deleting-mo-files-chunk');

        if (! is_numeric($dayCount) || ! is_numeric($chunk)) {
            throw new \Exception('Days before deleting MO files and chunk size should be a number');
        }

        $this->info(\Illuminate\Support\Carbon::now() . ' Начало удаления ссылок на старые файлы');
        Log::info('mo-file-links:remove - Запуск команды');

        $lastDate = Carbon::now()->subDays($dayCount)->startOfDay();

        $forms = DB::table('forms')
            ->select('forms.uuid')
            ->join('medic_forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->whereNotNull('medic_forms.photos')
            ->where(function (Builder $query) {
                $query->whereNotNull('medic_forms.photos')
                    ->orWhereNotNull('medic_forms.videos');
            })
            ->where('forms.created_at', '<', $lastDate->format('Y-m-d H:i:s'))
            ->limit($chunk)
            ->get();

        $ctr = 0;
        foreach ($forms->chunk(1000) as $chunk) {
            DB::table('medic_forms')
                ->whereIn('forms_uuid', $chunk->pluck('uuid'))
                ->update([
                    'photos' => null,
                    'videos' => null,
                ]);

            $ctr += count($chunk);
            $this->info(Carbon::now() . " Обработано $ctr записей");
        }

        $this->line(Carbon::now() . " Завершение работы. Ссылки на фото и видео удалены у $ctr медосмотров");
        Log::info("Завершение работы. Ссылки на фото и видео удалены у $ctr медосмотров");
    }
}
