<?php

namespace App\Console\Commands\Users;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class CheckUsersAndEntitiesRelationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-relations
                            {--u|--users : Пользователи без связанной сущности}
                            {--c|--companies : Компании без пользователя}
                            {--d|--drivers : Водители без пользователя}
                            {--with-trashed : Учитывать удаленные записи}
                            {--hash-id : Отображать HASH_ID, вместо ID (для компаний и водителей)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка отношений пользователей и бизнес-сущностей';

    private $withTrashed = false;

    private $printedField = 'id';

    public function handle()
    {
        $checkUsers = $this->option('users');
        $checkCompanies = $this->option('companies');
        $checkDrivers = $this->option('drivers');
        $this->withTrashed = $this->option('with-trashed');

        if ($this->option('hash-id')) {
            $this->printedField = 'hash_id';
        }

        if ($checkUsers) {
            $items = $this->checkUsers();
            $count = count($items);
            $this->print("Пользователи без сущности [ID] - ($count):", $items);
        }

        if ($checkCompanies) {
            $items = $this->checkCompanies();
            $count = count($items);
            $field = strtoupper($this->printedField);

            $this->print("Компании без пользователя [$field] - ($count):", $items);
        }

        if ($checkDrivers) {
            $items = $this->checkDrivers();
            $count = count($items);
            $field = strtoupper($this->printedField);

            $this->print("Водители без пользователя [$field] - ($count):", $items);
        }

        $this->info('Проверка завершена');
    }

    private function print(string $title, array $items)
    {
        $this->info($title);
        $this->comment(implode(', ', $items));
        $this->info('');
    }

    private function checkUsers(): array
    {
        return DB::table('users')
            ->select([
                'id',
            ])
            ->when(! $this->withTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereNull('entity_type')
            ->get()
            ->pluck('id')
            ->toArray();
    }

    private function checkCompanies(): array
    {
        return DB::table('companies')
            ->select([
                $this->printedField,
            ])
            ->when(! $this->withTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereNull('related_user_id')
            ->get()
            ->pluck($this->printedField)
            ->toArray();
    }

    private function checkDrivers(): array
    {
        return DB::table('drivers')
            ->select([
                $this->printedField,
            ])
            ->when(! $this->withTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereNull('related_user_id')
            ->get()
            ->pluck($this->printedField)
            ->toArray();
    }
}
