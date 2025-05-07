<?php

namespace App\Console\Commands\DataFix;

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
    protected $signature = 'data-fix:fix-created-at-in-forms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Исправление даты создания в осмотрах';

    public function handle()
    {
        $user = User::where('login', '=', User::DEFAULT_USER_LOGIN)->first();

        if (! $user) {
            $this->error('Не найден пользователь администратор');
        }

        $count = 0;

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
            ->where(function ($query) {
                $query->where('forms.type_anketa', '!=', FormTypeEnum::MEDIC)
                    ->orWhere('medic_forms.flag_pak', '=', FlagPakEnum::INTERNAL);
            })
            ->whereNull('employees.timezone')
            ->whereNotNull('employees.id')
            ->orderBy('forms.created_at')
            ->each(function (Form $form) use ($user, &$count) {
                try {
                    $form->timestamps = false;

                    $oldCreatedAt = Carbon::parse($form->created_at);
                    $newCreatedAt = $oldCreatedAt->copy()->addHours(3);

                    $form->created_at = $newCreatedAt;

                    $this->comment('Обновление осмотра ('.$form->id.') [created_at]: '.$oldCreatedAt.' -> '.$newCreatedAt);

                    event(new FormAction($user, $form, FormLogActionTypesEnum::CORRECTION));

                    $form->save();

                    $count++;
                } catch (\Exception $exception) {
                    DB::rollBack();
                    $this->error($exception->getMessage());
                    throw $exception;
                }
            });

        DB::commit();
        $this->info('Успешно завершено ('.$count.')');
    }
}
