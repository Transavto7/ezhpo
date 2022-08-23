<?php

// create, read, update and delete.
return [
    //============== Медосмотр =====================//
    [
        'name'        => 'medic_create',
        'description' => 'Медосмотры создание',
    ],
    [
        'name'        => 'medic_read',
        'description' => 'Медосмотры просмотр',
    ],
    [
        'name'        => 'medic_update',
        'description' => 'Медосмотры редактирование',
    ],
    [
        'name'        => 'medic_delete',
        'description' => 'Медосмотры удаление',
    ],
    [
        'name'        => 'medic_trash',
        'description' => 'Медосмотры удаление',
    ],

    //============== Техосмотр =====================//
    [
        'name'        => 'tech_create',
        'description' => 'Техосмотры - создание',
    ],
    [
        'name'        => 'tech_read',
        'description' => 'Техосмотры - просмотр',
    ],
    [
        'name'        => 'tech_update',
        'description' => 'Техосмотры - редактирование',
    ],
    [
        'name'        => 'tech_delete',
        'description' => 'Техосмотры - удаление',
    ],
    [
        'name'        => 'tech_trash',
        'description' => 'Техосмотры - корзина',
    ],

    //============== Журнал снятия отчетов с карт =============//
    [
        'name'        => 'map_report_create',
        'description' => 'Внести отчёт с карты',
    ],
    [
        'name'        => 'map_report_read',
        'description' => 'Реестр снятия отчетов с карт - Просмотр',
    ],

    //============== Журнал печати путевых листов =============//
    [
        'name'        => 'print_register_pl_create',
        'description' => 'Внести запись в Реестр печати ПЛ',
    ],

    //============== Журнал ПЛ =================================//
    [
        'name'        => 'journal_pl_create',
        'description' => 'Внести запись в Журнал ПЛ',
    ],
    [
        'name'        => 'journal_pl_read',
        'description' => 'Реестр ошибок СДПО - Просмотр',
    ],

    //============== Журнал инструктажей по БДД =============//
    [
        'name'        => 'journal_briefing_bdd_create',
        'description' => 'Инструктаж БДД - Создание',
    ],
    [
        'name'        => 'journal_briefing_bdd_read',
        'description' => 'Инструктаж БДД - Просмотр',
    ],
    [
        'name'        => 'journal_briefing_bdd_update',
        'description' => 'Инструктаж БДД - Редактирование',
    ],
    [
        'name'        => 'journal_briefing_bdd_delete',
        'description' => 'Инструктаж БДД - Удаление',
    ],
    [
        'name'        => 'journal_briefing_bdd_trash',
        'description' => 'Инструктаж БДД - Корзина',
    ],

    //============== Очередь утверждения =============//
    [
        'name'        => 'approval_queue_view',
        'description' => 'Очередь утверждения - просмотр',
    ],
    [
        'name'        => 'approval_queue_clear',
        'description' => 'Очередь утверждения - очистка',
    ],

    //============== Клиент =============//
    [
        'name'        => 'client_create',
        'description' => 'Добавить клиента',
    ],

    //============== Реестр ошибок СДПО =============//
    [
        'name'        => 'errors_2 sdpo_read',
        'description' => 'Реестр ошибок СДПО - Просмотр',
    ],

    //============== Отчеты =============//
    [
        'name'        => 'report_service_company_read',
        'description' => 'Отчет по услугам компании - Просмотр',
    ],
    [
        'name'        => 'report_schedule_pv_read',
        'description' => 'График работы пунктов выпуска - Просмотр',
    ],

    //============== Водители =============//
    [
        'name'        => 'drivers_create',
        'description' => 'Водители - Создание',
    ],
    [
        'name'        => 'drivers_read',
        'description' => 'Водители - Просмотр',
    ],
    [
        'name'        => 'drivers_update',
        'description' => 'Водители - Редактирование',
    ],
    [
        'name'        => 'drivers_delete',
        'description' => 'Водители - Удаление',
    ],

    //============== Автомобили =============//
    [
        'name'        => 'cars_create',
        'description' => 'Автомобили - Создание',
    ],
    [
        'name'        => 'cars_read',
        'description' => 'Автомобили - Просмотр',
    ],
    [
        'name'        => 'cars_update',
        'description' => 'Автомобили - Редактирование',
    ],
    [
        'name'        => 'cars_delete',
        'description' => 'Автомобили - Удаление',
    ],

    //============== Компании =============//
    [
        'name'        => 'company_create',
        'description' => 'Компании - Создание',
    ],
    [
        'name'        => 'company_read',
        'description' => 'Компании - Просмотр',
    ],
    [
        'name'        => 'company_update',
        'description' => 'Компании - Редактирование',
    ],
    [
        'name'        => 'company_delete',
        'description' => 'Компании - Удаление',
    ],
    [
        'name'        => 'company_sync',
        'description' => 'Компании - Синхронизация',
    ],

    //============== Услуги =============//
    [
        'name'        => 'service_create',
        'description' => 'Услуги - Создание',
    ],
    [
        'name'        => 'service_read',
        'description' => 'Услуги - Просмотр',
    ],
    [
        'name'        => 'service_update',
        'description' => 'Услуги - Редактирование',
    ],
    [
        'name'        => 'service_delete',
        'description' => 'Услуги - Удаление',
    ],

    //============== Скидки =============//
    [
        'name'        => 'discount_create',
        'description' => 'Скидки - Создание',
    ],
    [
        'name'        => 'discount_read',
        'description' => 'Скидки - Просмотр',
    ],
    [
        'name'        => 'discount_update',
        'description' => 'Скидки - Редактирование',
    ],
    [
        'name'        => 'discount_delete',
        'description' => 'Скидки - Удаление',
    ],

    //============== Инструктажи =============//
    [
        'name'        => 'briefings_create',
        'description' => 'Инструктажи - Создание',
    ],
    [
        'name'        => 'briefings_read',
        'description' => 'Инструктажи - Просмотр',
    ],
    [
        'name'        => 'briefings_update',
        'description' => 'Инструктажи - Редактирование',
    ],
    [
        'name'        => 'briefings_delete',
        'description' => 'Инструктажи - Удаление',
    ],

    //============== Настойки =============//
    [
        'name'        => 'settings_read',
        'description' => 'Настойки - Просмотр',
    ],
    //============== Система =============//
    [
        'name'        => 'system_create',
        'description' => 'Система - Создание',
    ],
    [
        'name'        => 'system_read',
        'description' => 'Система - Просмотр',
    ],
    [
        'name'        => 'system_update',
        'description' => 'Система - Редактирование',
    ],
    [
        'name'        => 'system_delete',
        'description' => 'Система - Удаление',
    ],
    //============== Системные настройки =============//
    [
        'name'        => 'settings_system_create',
        'description' => 'Системные настройки - Создание',
    ],
    [
        'name'        => 'settings_system_read',
        'description' => 'Системные настройки - Просмотр',
    ],
    [
        'name'        => 'settings_system_update',
        'description' => 'Системные настройки - Редактирование',
    ],
    [
        'name'        => 'settings_system_delete',
        'description' => 'Системные настройки - Удаление',
    ],
    //============== Города =============//
    [
        'name'        => 'city_create',
        'description' => 'Города - Создание',
    ],
    [
        'name'        => 'city_read',
        'description' => 'Города - Просмотр',
    ],
    [
        'name'        => 'city_update',
        'description' => 'Города - Редактирование',
    ],
    [
        'name'        => 'city_delete',
        'description' => 'Города - Удаление',
    ],
    //============== Пункты выпуска =============//
    [
        'name'        => 'pv_create',
        'description' => 'Пункты выпуска - Создание',
    ],
    [
        'name'        => 'pv_read',
        'description' => 'Пункты выпуска - Просмотр',
    ],
    [
        'name'        => 'pv_update',
        'description' => 'Пункты выпуска - Редактирование',
    ],
    [
        'name'        => 'pv_delete',
        'description' => 'Пункты выпуска - Удаление',
    ],
    //============== Сотрудники =============//
    [
        'name'        => 'employee_create',
        'description' => 'Сотрудники - Создание',
    ],
    [
        'name'        => 'employee_read',
        'description' => 'Сотрудники - Просмотр',
    ],
    [
        'name'        => 'employee_update',
        'description' => 'Сотрудники - Редактирование',
    ],
    [
        'name'        => 'employee_delete',
        'description' => 'Сотрудники - Удаление',
    ],
    //============== ПАК СДПО =============//
    [
        'name'        => 'pak_sdpo_create',
        'description' => 'ПАК СДПО - Создание',
    ],
    [
        'name'        => 'pak_sdpo_read',
        'description' => 'ПАК СДПО - Просмотр',
    ],
    [
        'name'        => 'pak_sdpo_update',
        'description' => 'ПАК СДПО - Редактирование',
    ],
    [
        'name'        => 'pak_sdpo_delete',
        'description' => 'ПАК СДПО - Удаление',
    ],
    //============== Контроль дат =============//
    [
        'name'        => 'date_control_create',
        'description' => 'Контроль дат - Создание',
    ],
    [
        'name'        => 'date_control_read',
        'description' => 'Контроль дат - Просмотр',
    ],
    [
        'name'        => 'date_control_update',
        'description' => 'Контроль дат - Редактирование',
    ],
    [
        'name'        => 'date_control_delete',
        'description' => 'Контроль дат - Удаление',
    ],
    //============== История изменения полей =============//
    [
        'name'        => 'story_field_create',
        'description' => 'История изменения полей - Создание',
    ],
    [
        'name'        => 'story_field_read',
        'description' => 'История изменения полей - Просмотр',
    ],
    [
        'name'        => 'story_field_update',
        'description' => 'История изменения полей - Редактирование',
    ],
    [
        'name'        => 'story_field_delete',
        'description' => 'История изменения полей - Удаление',
    ],
    //============== Реквизиты нашей компании =============//
    [
        'name'        => 'requisites_create',
        'description' => 'Реквизиты нашей компании - Создание',
    ],
    [
        'name'        => 'requisites_read',
        'description' => 'Реквизиты нашей компании - Просмотр',
    ],
    [
        'name'        => 'requisites_update',
        'description' => 'Реквизиты нашей компании - Редактирование',
    ],
    [
        'name'        => 'requisites_delete',
        'description' => 'Реквизиты нашей компании - Удаление',
    ],
    //============== Релизы =============//
    [
        'name'        => 'releases_read',
        'description' => 'Релизы - Просмотр',
    ],





//    [
//        'name'        => 'map_report_create',
//        'description' => 'Внести отчёт с карты',
//    ],
];
