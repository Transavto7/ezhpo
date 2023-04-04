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
                'access_permissions' => 'medic_create',
                'access_role' => '*'
            ],
            [
                'title' => 'Провести тех. осмотр',
                'css_class' => 'bg-blue text-white',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=tech',
                'access_permissions' => 'tech_create',
                'access_role' => '*'
            ],
            [
                'title' => 'Внести Отчёт с карты',
                'css_class' => 'bg-gray',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=report_cart',
                'access_permissions' => 'map_report_create',
                'access_role' => '*'
            ],
            [
                'title' => 'Внести запись в Реестр печати ПЛ',
                'css_class' => 'bg-gray',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=pechat_pl',
                'access_permissions' => 'print_register_pl_create',
                'access_role' => '*'
            ],
            [
                'title' => 'Внести Инструктаж БДД',
                'css_class' => 'bg-gray',
                'icon_class' => 'icon-padnote',
                'route_name' => '/profile/anketa?type=bdd',
                'access_permissions' => 'journal_briefing_bdd_create',
                'access_role' => '*'
            ],
            [
                'title' => 'Очередь утверждения',
                'icon_class' => 'fa fa-users',
                'slug' => 'pak_queue',
                'route_name' => '/home/pak_queue',
                'access_permissions' => 'approval_queue_view, approval_queue_clear',
                'access_role' => '*'
            ],
            [
                'title' => 'Добавить клиента',
                'css_class' => 'bg-info text-white',
                'slug' => 'add_client',
                'icon_class' => 'icon-user',
                'route_name' => '/pages/add_client',
                'access_permissions' => 'client_create',
                'access_role' => '*'
            ],
            [
                'title' => 'Журналы осмотров',
                'slug' => 'inspection_journal',
                'icon_class' => 'icon-grid',
                'is_header' => true,
                'access_permissions' => 'medic_create',
                'access_role' => '*',
                'children' => [
                    [
                        'title' => 'Журнал МО',
                        'slug' => 'medic_inspections_journal',
                        'icon_class' => 'fa fa-plus',
                        'route_name' => '/home/medic',
                        'access_permissions' => 'medic_read',
                        'access_role' => '*'
                    ],
                    [
                        'title' => 'Журнал ТО',
                        'slug' => 'tech_inspection_journal',
                        'icon_class' => 'fa fa-wrench',
                        'route_name' => '/home/tech',
                        'access_permissions' => 'tech_read',
                        'access_role' => '*'
                    ],
                    [
                        'title' => 'Журнал инструктажей БДД',
                        'slug' => 'bdd_inspections_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/home/bdd',
                        'access_permissions' => 'journal_briefing_bdd_read',
                        'access_role' => '*'
                    ],
                    [
                        'title' => 'Журнал печати ПЛ',
                        'slug' => 'pechat_pl_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/home/pechat_pl',
                        'access_permissions' => 'journal_pl_read',
                        'access_role' => '*'
                    ],
                    [
                        'title' => 'Реестр снятия отчетов с карт',
                        'slug' => 'report_cart_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/home/report_cart',
                        'access_permissions' => 'map_report_read',
                        'access_role' => '*'
                    ],
                    [
                        'title' => 'Реестр ошибок СДПО',
                        'slug' => 'errors_pak_log',
                        'icon_class' => 'fa fa-close',
                        'route_name' => '/home/pak',
                        'access_permissions' => 'errors_sdpo_read, errors_sdpo_create',
                        'access_role' => '*'
                    ]
                ]
            ],
            [
                'title' => 'Отчёты',
                'slug' => 'reports',
                'icon_class' => 'fa fa-area-chart',
                'is_header' => true,
                'access_permissions' => 'report_service_company_read, report_schedule_pv_read',
                'access_role' => '*',
                'children' => [
                    [
                        'title' => 'График работы пунктов выпуска',
                        'slug' => 'graph_pv',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/graph_pv',
                        'access_permissions' => 'report.get, graph_pv',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Отчёт по услугам компании',
                        'slug' => 'report_service_journal',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/journal',
                        'access_permissions' => 'report_service_company_read, report_service_company_export',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Отчет по количеству осмотров',
                        'slug' => 'report_dynamic_medic',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/dynamic/medic',
                        'access_permissions' => 'report_schedule_dynamic_mo',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Отчёт по услугам компании',
                        'slug' => 'report_service_journal_new',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/journal_new',
                        'access_permissions' => 'report_schedule_dynamic_mo',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Отчёты по работе сотрудников',
                        'slug' => 'report_employee_work',
                        'icon_class' => 'fa fa-book',
                        'route_name' => '/report/work',
                        'access_permissions' => 'report_schedule_dynamic_mo',
                        'access_role' => '*',
                    ],
                ]
            ],
            [
                'title' => 'CRM',
                'slug' => 'crm',
                'icon_class' => 'icon-interface-windows',
                'is_header' => true,
                'access_permissions' => 'medic_create',
                'access_role' => '*',
                'children' => [
                    [
                        'title' => 'Договор',
                        'slug' => 'contract',
                        'route_name' => '/contract',
                        'access_permissions' => 'contract_read, contract_create',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Водители',
                        'slug' => 'drivers',
                        'route_name' => '/elements/Driver',
                        'access_permissions' => 'driver_read, drivers_create',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Автомобили',
                        'slug' => 'cars',
                        'route_name' => '/elements/Car',
                        'access_permissions' => 'cars_read, cars_create',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Компании',
                        'slug' => 'companies',
                        'route_name' => '/elements/Company',
                        'access_permissions' => 'company_read, company_create',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Услуги',
                        'slug' => 'products',
                        'route_name' => '/elements/Product',
                        'access_permissions' => 'service_read, service_create',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Скидки',
                        'slug' => 'discounts',
                        'route_name' => '/elements/Discount',
                        'access_permissions' => 'discount_read, discount_create',
                        'access_role' => '*',
                    ],
                    [
                        'title' => 'Виды инструктажей',
                        'slug' => 'instructions',
                        'route_name' => '/elements/Instr',
                        'access_permissions' => 'briefings_read, briefings_create',
                        'access_role' => '*',
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
