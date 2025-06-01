<?php

namespace App\Console\Commands\ExportForms;

use App\Enums\FlagPakEnum;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class ExportMedicFormsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-medic-forms-from-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Экспорт осмотров';

    public function handle()
    {
        $this->info('Начало экспорта осмотров');

        $data = [];

        $forms = DB::table('forms')
            ->select([
                'forms.id',
                'forms.uuid',
                'forms.type_anketa',
                'forms.deleted_id',
                'forms.user_id',
                'forms.user_eds',
                'forms.user_validity_eds_start',
                'forms.user_validity_eds_end',
                'forms.point_id',
                'forms.driver_id',
                'forms.company_id',
                'forms.date',
                'forms.realy',
                'forms.created_at',
                'forms.updated_at',
                'forms.deleted_at',
                'medic_forms.forms_uuid as medic_forms_forms_uuid',
                'medic_forms.type_view as medic_forms_type_view',
                'medic_forms.operator_id as medic_forms_operator_id',
                'medic_forms.old_operator_id as medic_forms_old_operator_id',
                'medic_forms.driver_group_risk as medic_forms_driver_group_risk',
                'medic_forms.admitted as medic_forms_admitted',
                'medic_forms.complaint as medic_forms_complaint',
                'medic_forms.condition_visible_sliz as medic_forms_condition_visible_sliz',
                'medic_forms.condition_koj_pokr as medic_forms_condition_koj_pokr',
                'medic_forms.pressure as medic_forms_pressure',
                'medic_forms.tonometer as medic_forms_tonometer',
                'medic_forms.t_people as medic_forms_t_people',
                'medic_forms.pulse as medic_forms_pulse',
                'medic_forms.alcometer_mode as medic_forms_alcometer_mode',
                'medic_forms.alcometer_result as medic_forms_alcometer_result',
                'medic_forms.proba_alko as medic_forms_proba_alko',
                'medic_forms.test_narko as medic_forms_test_narko',
                'medic_forms.med_view as medic_forms_med_view',
                'medic_forms.photos as medic_forms_photos',
                'medic_forms.videos as medic_forms_videos',
                'medic_forms.protokol_path as medic_forms_protokol_path',
                'medic_forms.closing_path as medic_forms_closing_path',
                'medic_forms.is_dop as medic_forms_is_dop',
                'medic_forms.period_pl as medic_forms_period_pl',
                'medic_forms.result_dop as medic_forms_result_dop',
                'medic_forms.terminal_id as medic_forms_terminal_id',
                'medic_forms.old_terminal_id as medic_forms_old_terminal_id',
                'medic_forms.flag_pak as medic_forms_flag_pak',
                'medic_forms.day_hash as medic_forms_day_hash',
                'medic_forms.comments as medic_forms_comments',
            ])
            ->leftJoin('medic_forms', 'medic_forms.forms_uuid', '=', 'forms.uuid')
            ->where('forms.date', '>=', '2025-05-20')
            ->where('forms.date', '<=', '2025-05-30')
            ->where('forms.type_anketa', '=', 'medic')
            ->where('medic_forms.flag_pak', '=', FlagPakEnum::SDPO_A)
            ->get();

        $this->comment('Осмотров найдено: '.count($forms)."\n");

        $index = 1;
        foreach ($forms as $form) {
            $logs = DB::table('form_events')
                ->select([
                    'id',
                    'uuid',
                    'form_uuid',
                    'event_type',
                    'payload',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'model_type',
                ])
                ->where('form_events.form_uuid', '=', $form->uuid)
                ->where('form_events.created_at', '>=', '2025-05-20')
                ->where('form_events.created_at', '<=', '2025-05-30')
                ->get()
                ->toArray();

            $data[] = [
                'form' => [
                    'id' => $form->id,
                    'uuid' => $form->uuid,
                    'type_anketa' => $form->type_anketa,
                    'deleted_id' => $form->deleted_id,
                    'user_id' => $form->user_id,
                    'user_eds' => $form->user_eds,
                    'user_validity_eds_start' => $form->user_validity_eds_start,
                    'user_validity_eds_end' => $form->user_validity_eds_end,
                    'point_id' => $form->point_id,
                    'driver_id' => $form->driver_id,
                    'company_id' => $form->company_id,
                    'date' => $form->date,
                    'realy' => $form->realy,
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                    'deleted_at' => $form->deleted_at,
                ],
                'medic_form' => [
                    'forms_uuid' => $form->medic_forms_forms_uuid,
                    'type_view' => $form->medic_forms_type_view,
                    'operator_id' => $form->medic_forms_operator_id,
                    'old_operator_id' => $form->medic_forms_old_operator_id,
                    'driver_group_risk' => $form->medic_forms_driver_group_risk,
                    'admitted' => $form->medic_forms_admitted,
                    'complaint' => $form->medic_forms_complaint,
                    'condition_visible_sliz' => $form->medic_forms_condition_visible_sliz,
                    'condition_koj_pokr' => $form->medic_forms_condition_koj_pokr,
                    'pressure' => $form->medic_forms_pressure,
                    'tonometer' => $form->medic_forms_tonometer,
                    't_people' => $form->medic_forms_t_people,
                    'pulse' => $form->medic_forms_pulse,
                    'alcometer_mode' => $form->medic_forms_alcometer_mode,
                    'alcometer_result' => $form->medic_forms_alcometer_result,
                    'proba_alko' => $form->medic_forms_proba_alko,
                    'test_narko' => $form->medic_forms_test_narko,
                    'med_view' => $form->medic_forms_med_view,
                    'photos' => $form->medic_forms_photos,
                    'videos' => $form->medic_forms_videos,
                    'protokol_path' => $form->medic_forms_protokol_path,
                    'closing_path' => $form->medic_forms_closing_path,
                    'is_dop' => $form->medic_forms_is_dop,
                    'period_pl' => $form->medic_forms_period_pl,
                    'result_dop' => $form->medic_forms_result_dop,
                    'terminal_id' => $form->medic_forms_terminal_id,
                    'old_terminal_id' => $form->medic_forms_old_terminal_id,
                    'flag_pak' => $form->medic_forms_flag_pak,
                    'day_hash' => $form->medic_forms_day_hash,
                    'comments' => $form->medic_forms_comments,
                ],
                'logs' => $logs,
            ];

            $this->comment("$index. Записей в журнале найдено [{$form->id}]: ".count($logs));
            $index++;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'form_export_'.Carbon::now()->format('Y-m-d_h-i-s').'.json';

        file_put_contents(storage_path("app/public/$fileName"), $json);

        $this->info("\nЭкспорт завершен");
    }
}
