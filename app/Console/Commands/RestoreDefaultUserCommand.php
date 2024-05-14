<?php

namespace App\Console\Commands;

use App\GenerateHashIdTrait;
use App\Role;
use App\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class RestoreDefaultUserCommand extends Command
{
    use GenerateHashIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:restore-default-user
                            {password=Q@Zwsx123! : Пароль базового пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Восстановление базового пользователя';

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
     * @throws Exception
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $baseUserLogin = User::DEFAULT_USER_LOGIN;

            $password = $this->argument('password');
            $password = Hash::make($password);

            $adminRole = Role::query()
                ->where('name', 'admin')
                ->first();
            if (!$adminRole) {
                throw new Exception('Не найдена базовая роль Администратора');
            }

            /** @var User $existUser */
            $existUser = User::query()
                ->withoutGlobalScope('hideDefaultUser')
                ->withTrashed()
                ->where('login', $baseUserLogin)
                ->first();

            if (!$existUser) {
                $validator = function (int $hashId) {
                    if (User::where('hash_id', $hashId)->first()) {
                        return false;
                    }

                    return true;
                };

                $userHashId = $this->generateHashId(
                    $validator,
                    config('app.hash_generator.user.min'),
                    config('app.hash_generator.user.max'),
                    config('app.hash_generator.user.tries')
                );

                $data = [
                    'hash_id' => $userHashId,
                    'login' => $baseUserLogin,
                    'password' => $password,
                    'api_token' => Hash::make(date('H:i:s') . sha1($userHashId)),
                    'name' => 'Администратор',
                    'role' => 777,
                    'email' => $baseUserLogin
                ];

                $existUser = User::create($data);
            } else {
                $existUser->update([
                    'password' => $password,
                    'role' => 777,
                    'deleted_at' => null,
                    'deleted_id' => null
                ]);
            }

            $existUser->roles()->syncWithoutDetaching([$adminRole->id]);

            DB::commit();

            $this->info('Пользователь успешно восстановлен');
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->error("Ошибка: " . $exception->getMessage());
        }
    }
}
