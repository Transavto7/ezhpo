<?php

namespace App\Console\Commands\SplitUsers;

use App\Enums\FlagPakEnum;
use App\Enums\FormLogActionTypesEnum;
use App\Enums\FormTypeEnum;
use App\Events\Forms\FormAction;
use App\Models\Forms\Form;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class FixCreatedAtInFormsCommand extends Command
{
    const UPDATE_DATE = '2025-04-29 12:01:00';

    const FIX_DATE = '2025-04-30 04:12:00';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:fix-medic-forms-created-at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Экспорт осмотров с неправильной датой создания';

    public function handle()
    {
        $logData = [];
        $user = User::where('login', '=', User::DEFAULT_USER_LOGIN)->first();

        if (! $user) {
            $this->error('Не найден пользователь администратор');
        }

        DB::beginTransaction();

        Form::withTrashed()
            ->select([
                'forms.*',
            ])
            ->leftJoin('employees', 'employees.related_user_id', '=', 'forms.user_id')
            ->leftJoin('medic_forms', 'medic_forms.forms_uuid', '=', 'forms.uuid')
            ->where('forms.created_at', '>=', self::UPDATE_DATE)
            ->where('forms.created_at', '<', self::FIX_DATE)
            ->where('forms.updated_at', '>=', self::UPDATE_DATE)
            ->whereNull('employees.timezone')
            ->whereNotNull('employees.id')
            ->where(function ($query) {
                $query->where('forms.type_anketa', '!=', FormTypeEnum::MEDIC)
                    ->orWhere('medic_forms.flag_pak', '=', FlagPakEnum::INTERNAL);
            })
            ->orderBy('forms.created_at')
            ->each(function (Form $form) use ($user, &$logData) {
                try {
                    $oldValue = $form->created_at;
                    $newValue = $oldValue->copy()->addHours(3);

                    $form->created_at = $form->created_at->copy()->addHours(3);
                    event(new FormAction($user, $form, FormLogActionTypesEnum::CORRECTION));

                    $this->comment("Обновление created_at осмотра [$form->id]: $oldValue -> $newValue");
                    $logData[] = [
                        'id' => $form->id,
                        'old_created_at' => $oldValue->format('Y-m-d H:i:s'),
                        'new_created_at' => $newValue->format('Y-m-d H:i:s'),
                    ];

                    $form->save();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    $this->error($exception->getMessage());
                    throw $exception;
                }
            });

        DB::commit();

        $json = json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'form_created_at_fix_'.Carbon::now()->format('Y-m-d_H-i-s').'.json';

        file_put_contents(storage_path("app/$fileName"), $json);

        $this->comment("\nОбновлено осмотров: ".count($logData)."\n");
        $this->info('Успешно завершено');
    }
}
