<?php

namespace App\Console\Commands\DataFix;

use App\Employee;
use App\Enums\FormLogActionTypesEnum;
use App\Events\Forms\FormAction;
use App\Models\Forms\Form;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

final class FixMedicInFormsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-fix:fix-medic-in-forms {--restore-file= : файл для восстановления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Замена медиков в осмотрах';

    public function handle()
    {
        $path = $this->option('restore-file');

        if (! $path) {
            $this->error('Не указан файл для восстановления.');

            return;
        }

        if (! File::exists($path)) {
            $this->error("Файл по пути {$path} не существует.");

            return;
        }

        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'json') {
            $this->error('Файл должен быть в формате JSON.');

            return;
        }

        $user = User::where('login', '=', User::DEFAULT_USER_LOGIN)->first();

        if (! $user) {
            $this->error('Не найден пользователь администратор');
        }

        $fileContent = file_get_contents($path);
        $data = json_decode($fileContent, true)['items'];

        DB::beginTransaction();
        try {
            foreach ($data as $item) {
                $medic = Employee::withTrashed()->where('hash_id', '=', $item['medic_hash_id'])->first();
                $form = Form::withTrashed()->where('id', '=', $item['form_id'])->first();

                if (! $medic) {
                    $this->error("Медик {$item['medic_hash_id']} не найден (осмотр: {$item['form_id']})");
                }

                if (! $form) {
                    $this->error("Осмотр {$item['form_id']} не найден (медик: {$item['medic_hash_id']})");
                }

                $form->user_id = $medic->related_user_id;
                event(new FormAction($user, $form, FormLogActionTypesEnum::CORRECTION));

                $form->save();
            }

            DB::commit();
            $this->info('Успешно завершено');
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->error($exception->getMessage());
            throw $exception;
        }
    }
}
