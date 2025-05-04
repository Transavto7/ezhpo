<?php

namespace App\Console\Commands\SplitUsers;

use App\Enums\FormLogActionTypesEnum;
use App\Events\Forms\FormAction;
use App\Models\Forms\Form;
use App\User;
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
    protected $signature = 'split-users:export-forms-with-wrong-created-at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Экспорт осмотров с неправильной датой создания';

    public function handle()
    {
        $user = User::where('login', '=', User::DEFAULT_USER_LOGIN)->first();

        if (! $user) {
            $this->error('Не найден пользователь администратор');
        }

        DB::beginTransaction();

        Form::withTrashed()
            ->leftJoin('employees', 'employees.related_user_id', '=', 'forms.user_id')
            ->where('forms.created_at', '>=', self::UPDATE_DATE)
            ->where('forms.created_at', '<', self::FIX_DATE)
            ->where('forms.updated_at', '>=', self::UPDATE_DATE)
            ->whereNull('employees.timezone')
            ->whereNotNull('employees.id')
            ->each(function (Form $form) use ($user) {
                try {
                    $form->created_at = $form->created_at->copy()->addHours(3);
                    event(new FormAction($user, $form, FormLogActionTypesEnum::CORRECTION));

                    $form->save();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    $this->error($exception->getMessage());
                    throw $exception;
                }
            });

        DB::commit();
        $this->info('Успешно завершено');
    }
}
