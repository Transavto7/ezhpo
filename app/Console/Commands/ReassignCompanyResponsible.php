<?php

namespace App\Console\Commands;

use App\Company;
use App\Role;
use App\User;
use Illuminate\Console\Command;

class ReassignCompanyResponsible extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reassign:responsible';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle(): int
    {
        $namesFirst = [ // Менеджер по работе с общими клиентами
            'Ярош Елизавета Юрьевна',
            'Кочерженко Владимир Витальевич',
            'Носов Виталий Сергеевич',
            'Лебедева Людмила Николаевна',
            'Володин Сергей Сергеевич',
            'Дудник Маргарита Александровна',
            'Беляевский Дмитрий',
            'Клочков Андрей Валерьевич',
            'Урманчиев Наиль Фаритович',
            'Ульянова Рената',
            'Рук Татьяна Меркулова',
            'Миронова Екатерина Петровна',
            'Новиков Артем Игоревич',
            'Радковская Ольга Александровна',
            'Рыкун Наталья Сергеевна',
            'Тимаева Анастасия Усмановна',
            'Бахтеева Анастасия Сергеевна',
            'Галицкая Анна Алексеевна',
            'Любовь Шафер Юрьевна',
            'Татарченкова Светлана Юрьевна',
            'Капитонова Дэниза Рамилевна',
            'Михайлова Татьяна Сергеевна',
            'Работникова Ирина Юрьевна',
            'Бекмухамбетова Валентина Геннадьевна',
            'Скрипченко Александр Александрович',
            'Колесникова Ангелина Михайловна',
            'Нагаева Эльвина Энверовна'
        ];

        $namesSecond = [ // Менеджер по работе с клиентами
            'Кузнецова Ирина Викторовна',
            'Галахин Дмитрий',
            'Аникина Елена',
            'Волохова Зарина',
            'Булыгина Нина',
            'Асанова Усние',
            'Наталья Колоскова'
        ];

        \DB::transaction(function () use ($namesFirst, $namesSecond) {

            $generalAccountManager = Role::where(['guard_name' => 'Менеджер по работе с общими клиентами'])->first();
            $accountManager = Role::where(['guard_name' => 'Менеджер по работе с клиентами'])->first();

            if (!($generalAccountManager && $accountManager)) {
                $generalAccountManager = Role::updateOrCreate([
                    'name' => 'general_account_manager',
                    'guard_name' => 'Менеджер по работе с общими клиентами',
                ]);

                $accountManager = Role::updateOrCreate([
                    'name' => 'account_manager',
                    'guard_name' => 'Менеджер по работе с общими клиентами',
                ]);
            }

            /**
             * @var User $accountManagerUser
             * @var User $generalAccountManagerUser
             */

            $accountManagerUser = $accountManager->users()->first();
            $generalAccountManagerUser = $generalAccountManager->users()->first();

            if (!($accountManagerUser && $generalAccountManagerUser)) {
                $accountManagerUser = factory(User::class)->create([
                    'name' => 'Менеджер по работе с клиентами', 'email' => 'am@ta-7.ru'
                ]);

                $accountManagerUser->roles()->attach($accountManager);

                $generalAccountManagerUser = factory(User::class)->create([
                    'name' => 'Менеджер по работе с общими клиентами', 'email' => 'gam@ta-7.ru'
                ]);
                $generalAccountManagerUser->roles()->attach($generalAccountManager);
            }


            $companies = Company::whereIn('user_id',
                User::whereIn('name', array_merge($namesSecond, $namesFirst))->select('id'))
                ->with(['responsible'])
                ->cursor();


            foreach ($companies as $company) {
                /** @var Company $company */
                if (in_array($company->responsible->name, $namesFirst)) {
                    $company->responsible()
                        ->associate($generalAccountManagerUser)
                        ->save();
                }

                if (in_array($company->responsible->name, $namesSecond)) {
                    $company->responsible()
                        ->associate($accountManagerUser)
                        ->save();
                }
            }


        });

        return 1;

    }
}
