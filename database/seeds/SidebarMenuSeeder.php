<?php

use Illuminate\Database\Seeder;

class SidebarMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\SideBarMenuItem::truncate();
        $models = [
            [
                'title' => 'Провести мед. осмотр',
                'css_class' => 'bg-red text-white',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=medic',
                'access_permissions' => 'medic_create'
            ],
            [
                'title' => 'Провести тех. осмотр',
                'css_class' => 'bg-blue text-white',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=tech',
                'access_permissions' => 'tech_create'
            ],
            [
                'title' => 'Внести Отчёт с карты',
                'css_class' => 'bg-gray',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=report_cart',
                'access_permissions' => 'map_report_create'
            ],
            [
                'title' => 'Внести запись в Реестр печати ПЛ',
                'css_class' => 'bg-gray',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=pechat_pl',
                'access_permissions' => 'print_register_pl_create'
            ],
            [
                'title' => 'Внести Инструктаж БДД',
                'css_class' => 'bg-gray',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=bdd',
                'access_permissions' => 'journal_briefing_bdd_create'
            ],
            [
                'title' => 'Очередь утверждения',
                'icon_class' => 'fa fa-users',
                'slug' => 'pak_queue',
                'route_name' => '/home/pak_queue',
                'access_permissions' => 'approval_queue_view, approval_queue_clear'
            ],
            [
                'title' => 'Добавить клиента',
                'css_class' => 'bg-info text-white',
                'slug' => 'add_client',
                'icon_class' => 'icon-user',
                'route_name' => '/pages/add_client',
                'access_permissions' => 'client_create',
            ],
            [
                'title' => 'Журналы осмотров',
                'slug' => 'inspection_journal',
                'icon_class' => 'icon-grid',
                'is_header' => true,
                'children' => [
                    [
                        'title' => 'Журнал МО',
                        'slug' => 'medic_inspections_journal',
                        'icon_class' => 'fa fa-plus',
                        'route_name' => '/home/medic',
                    ],
                    [
                        'title' => 'Журнал ТО',
                        'slug' => 'tech_inspection_journal',
                        'icon_class' => 'fa fa-wrench',
                        'route_name' => '/home/tech',
                    ],
                    [
                        'title' => 'Журнал инструктажей БДД',
                        'slug' => 'bdd_inspections_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/home/bdd',
                    ],
                    [
                        'title' => 'Журнал печати ПЛ',
                        'slug' => 'pechat_pl_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/home/pechat_pl',
                    ],
                    [
                        'title' => 'Реестр снятия отчетов с карт',
                        'slug' => 'report_cart_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/home/report_cart',
                    ],
                    [
                        'title' => 'Реестр ошибок СДПО',
                        'slug' => 'errors_pak_log',
                        'icon_class' => 'fa fa-close',
                        'route_name' => '/home/pak',
                    ]
                ]
            ],
            [
                'title' => 'Отчёты',
                'slug' => 'reports',
                'icon_class' => 'fa fa-area-chart',
                'is_header' => true,
                'children' => [
                    [
                        'title' => 'График работы пунктов выпуска',
                        'slug' => 'graph_pv',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/graph_pv',
                    ],
                    [
                        'title' => 'Отчёт по услугам компании',
                        'slug' => 'report_service_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/journal',
                    ],
                    [
                        'title' => 'Отчет по количеству осмотров',
                        'slug' => 'report_dynamic_medic',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/dynamic/medic',
                    ],
                    [
                        'title' => 'Отчёт по услугам компании',
                        'slug' => 'report_service_journal_new',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/journal_new',
                    ],
                    [
                        'title' => 'Отчёты по работе сотрудников',
                        'slug' => 'report_employee_work',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/work',
                    ],
                ]
            ],
            [
                'title' => 'CRM',
                'slug' => 'crm',
                'icon_class' => 'icon-interface-windows',
                'is_header' => true,
                'children' => [
                    [
                        'title' => 'Договор',
                        'slug' => 'contract',
                        'route_name' => '/contract',
                    ],
                    [
                        'title' => 'Водители',
                        'slug' => 'drivers',
                        'route_name' => '/elements/Driver',
                    ],
                    [
                        'title' => 'Автомобили',
                        'slug' => 'cars',
                        'route_name' => '/elements/Car',
                    ],
                    [
                        'title' => 'Компании',
                        'slug' => 'companies',
                        'route_name' => '/elements/Company',
                    ],
                    [
                        'title' => 'Услуги',
                        'slug' => 'products',
                        'route_name' => '/elements/products',
                    ],
                    [
                        'title' => 'Скидки',
                        'slug' => 'discounts',
                        'route_name' => '/elements/Product',
                    ],
                    [
                        'title' => 'Виды инструктажей',
                        'slug' => 'instructions',
                        'route_name' => '/elements/Instr',
                    ],
                ]
            ]
        ];

        foreach ($models as $model) {
            /** @var \App\SideBarMenuItem $result */
            if (isset($model['children'])) {
                $children = $model['children'];
                unset($model['children']);
                $result = factory(\App\SideBarMenuItem::class)->create($model);
                foreach ($children as $child) {
                    $child['parent_id'] = $result->id;
                    factory(\App\SideBarMenuItem::class)->create($child);
                }
            } else {
                factory(\App\SideBarMenuItem::class)->create($model);
            }
        }


    }
}
