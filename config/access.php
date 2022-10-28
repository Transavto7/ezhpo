<?php

// create, read, update and delete.
return [
    //============== Медосмотр =====================//
    [
        'name'        => 'medic_create',
        'description' => 'Медосмотры - Создание',
    ],
    [
        'name'        => 'medic_read',
        'description' => 'Медосмотры - Просмотр',
    ],
    [
        'name'        => 'medic_update',
        'description' => 'Медосмотры - Редактирование',
    ],
    [
        'name'        => 'medic_delete',
        'description' => 'Медосмотры - Удаление',
    ],
    [
        'name'        => 'medic_trash',
        'description' => 'Медосмотры - Корзина',
    ],
    [
        'name'        => 'medic_export',
        'description' => 'Медосмотры - Экспорт',
    ],
    [
        'name'        => 'medic_export_prikaz',
        'description' => 'Медосмотры - Экспорт приказ',
    ],

    //============== Техосмотр =====================//
    [
        'name'        => 'tech_create',
        'description' => 'Техосмотры - Создание',
    ],
    [
        'name'        => 'tech_read',
        'description' => 'Техосмотры - Просмотр',
    ],
    [
        'name'        => 'tech_update',
        'description' => 'Техосмотры - Редактирование',
    ],
    [
        'name'        => 'tech_delete',
        'description' => 'Техосмотры - Удаление',
    ],
    [
        'name'        => 'tech_trash',
        'description' => 'Техосмотры - Корзина',
    ],
    [
        'name'        => 'tech_export',
        'description' => 'Техосмотры - Экспорт',
    ],
    [
        'name'        => 'tech_export_prikaz',
        'description' => 'Техосмотры - Экспорт приказ',
    ],
    [
        'name'        => 'tech_export_prikaz_pl',
        'description' => 'Техосмотры - Экспорт приказ ПЛ',
    ],

    //============== Журнал снятия отчетов с карт report_cart =============//
    [
        'name'        => 'map_report_create',
        'description' => 'Реестр снятия отчетов с карт - Создание',
    ],
    [
        'name'        => 'map_report_read',
        'description' => 'Реестр снятия отчетов с карт - Просмотр',
    ],
    [
        'name'        => 'map_report_update',
        'description' => 'Реестр снятия отчетов с карт - Редактирование',
    ],
    [
        'name'        => 'map_report_delete',
        'description' => 'Реестр снятия отчетов с карт - Удаление',
    ],
    [
        'name'        => 'map_report_trash',
        'description' => 'Реестр снятия отчетов с карт - Корзина',
    ],
    [
        'name'        => 'map_report_export',
        'description' => 'Реестр снятия отчетов с карт - Экспорт',
    ],
    [
        'name'        => 'map_report_export_prikaz',
        'description' => 'Реестр снятия отчетов с карт - Экспорт приказ',
    ],

    //============== Журнал печати путевых листов =============//
    [
        'name'        => 'print_register_pl_create',
        'description' => 'Журнал печати ПЛ - Создание',
    ],

    //============== Журнал печать ПЛ pechat_pl =================================//
    [
        'name'        => 'journal_pl_read',
        'description' => 'Журнал печати ПЛ - Просмотр',
    ],
    [
        'name'        => 'journal_pl_update',
        'description' => 'Журнал печати ПЛ - Редактирование',
    ],
    [
        'name'        => 'journal_pl_trash',
        'description' => 'Журнал печати ПЛ - Корзина',
    ],
    [
        'name'        => 'journal_pl_delete',
        'description' => 'Журнал печати ПЛ - Удаление',
    ],
    [
        'name'        => 'journal_pl_export',
        'description' => 'Журнал печати ПЛ - Экспорт',
    ],
    [
        'name'        => 'journal_pl_export_prikaz',
        'description' => 'Журнал печати ПЛ - Экспорт приказ',
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
    [
        'name'        => 'journal_briefing_bdd_export',
        'description' => 'Инструктаж БДД - Экспорт',
    ],
    [
        'name'        => 'journal_briefing_bdd_export_prikaz',
        'description' => 'Инструктаж БДД - Экспорт приказ',
    ],

    //============== Очередь утверждения =============//
    [
        'name'        => 'approval_queue_view',
        'description' => 'Очередь утверждения - Просмотр',
    ],
    [
        'name'        => 'approval_queue_clear',
        'description' => 'Очередь утверждения - Очистка',
    ],

    //============== Клиент =============//
    [
        'name'        => 'client_create',
        'description' => 'Добавить клиента',
    ],

    //============== Реестр ошибок СДПО =============//
    [
        'name'        => 'errors_sdpo_read',
        'description' => 'Реестр ошибок СДПО - Просмотр',
    ],
    [
        'name'        => 'errors_sdpo_update',
        'description' => 'Реестр ошибок СДПО - Редактирование',
    ],
    [
        'name'        => 'errors_sdpo_delete',
        'description' => 'Реестр ошибок СДПО - Удаление',
    ],
    //    [
    //        'name'        => 'errors_sdpo_create',
    //        'description' => 'Реестр ошибок СДПО - Создания',
    //    ],
    [
        'name'        => 'errors_sdpo_trash',
        'description' => 'Реестр ошибок СДПО - Корзина',
    ],

    //============== Отчеты =============//
    [
        'name'        => 'report_service_company_read',
        'description' => 'Отчет по услугам компании - Просмотр',
    ],
    [
        'name'        => 'report_service_company_export',
        'description' => 'Отчет по услугам компании - Экспорт',
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
    [
        'name'        => 'drivers_trash_read',
        'description' => 'Водители - Корзина',
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
    [
        'name'        => 'cars_trash_read',
        'description' => 'Автомобили - Корзина',
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
//    [
//        'name'        => 'company_sync',
//        'description' => 'Компании - Синхронизация',
//    ],
    [
        'name'        => 'company_trash_read',
        'description' => 'Компании - Корзина',
    ],

    //============== Услуги - ЗКЩВГСЕ =============//
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
    [
        'name'        => 'service_trash_read',
        'description' => 'Услуги - Корзина',
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
    [
        'name'        => 'discount_trash_read',
        'description' => 'Скидки - Корзина',
    ],

    //============== Виды инструктажей =============//
    [
        'name'        => 'briefings_create',
        'description' => 'Виды инструктажей - Создание',
    ],
    [
        'name'        => 'briefings_read',
        'description' => 'Виды инструктажей - Просмотр',
    ],
    [
        'name'        => 'briefings_update',
        'description' => 'Виды инструктажей - Редактирование',
    ],
    [
        'name'        => 'briefings_delete',
        'description' => 'Виды инструктажей - Удаление',
    ],
    [
        'name'        => 'briefings_trash_read',
        'description' => 'Виды инструктажей - Корзина',
    ],

    //============== Система =============//
    [
        'name'        => 'system_read',
        'description' => 'Система - Просмотр',
    ],
    [
        'name'        => 'system_delete',
        'description' => 'Система - Удаление',
    ],
    [
        'name'        => 'system_update',
        'description' => 'Система - Редактирование',
    ],
    [
        'name'        => 'system_trash',
        'description' => 'Система - Корзина',
    ],
    //============== Система =============//
    //    [
    //        'name'        => 'system_create',
    //        'description' => 'Система - Создание',
    //    ],
    //    [
    //        'name'        => 'system_read',
    //        'description' => 'Система - Просмотр',
    //    ],
    //    [
    //        'name'        => 'system_update',
    //        'description' => 'Система - Редактирование',
    //    ],
    //    [
    //        'name'        => 'system_delete',
    //        'description' => 'Система - Удаление',
    //    ],
    //============== Системные настройки =============//
    [
        'name'        => 'settings_system_read',
        'description' => 'Системные настройки - Просмотр',
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
    [
        'name'        => 'city_trash_read',
        'description' => 'Города - Корзина',
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
    [
        'name'        => 'pv_trash_read',
        'description' => 'Пункты выпуска - Корзина',
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
    [
        'name'        => 'employee_trash',
        'description' => 'Сотрудники - Корзина',
    ],
    //============== Роли =============//
    [
        'name'        => 'group_create',
        'description' => 'Роли - Создание',
    ],
    [
        'name'        => 'group_read',
        'description' => 'Роли - Просмотр',
    ],
    [
        'name'        => 'group_update',
        'description' => 'Роли - Редактирование',
    ],
    [
        'name'        => 'group_delete',
        'description' => 'Роли - Удаление',
    ],
    [
        'name'        => 'group_trash',
        'description' => 'Роли - Корзина',
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
    [
        'name'        => 'pak_sdpo_trash',
        'description' => 'ПАК СДПО - Корзина',
    ],
    [
        'name'        => 'pak_sdpo_export',
        'description' => 'ПАК СДПО - Экспорт',
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
    [
        'name'        => 'date_control_trash',
        'description' => 'Контроль дат - Корзина',
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
    [
        'name'        => 'story_field_trash',
        'description' => 'История изменения полей - Корзина',
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
    [
        'name'        => 'requisites_trash_read',
        'description' => 'Реквизиты нашей компании - Корзина',
    ],
    //============== Релизы =============//
    [
        'name'        => 'releases_read',
        'description' => 'Релизы - Просмотр',
    ],


    //============== Подсказки полей =============//
    [
        'name'       => 'field_prompt_read',
        'description' => 'Подсказки полей - Просмотр',
    ],
    [
        'name'       => 'field_prompt_edit',
        'description' => 'Подсказки полей - Редактирование',
    ],
    [
        'name'       => 'field_prompt_delete',
        'description' => 'Подсказки полей - Удаление',
    ],
    [
        'name'       => 'field_prompt_trash',
        'description' => 'Подсказки полей - Корзина',
    ],

    //============== Договор =============//
    [
        'name'       => 'contract_read',
        'description' => 'Договор - Просмотр',
    ],
    [
        'name'       => 'contract_create',
        'description' => 'Договор - Создание',
    ],
    [
        'name'       => 'contract_edit',
        'description' => 'Договор - Редактирование',
    ],
    [
        'name'       => 'contract_delete',
        'description' => 'Договор - Удаление',
    ],
    [
        'name'       => 'contract_trash',
        'description' => 'Договор - Корзина',
    ],

    //============== Кому отправлять СМС && Кому звонить при отстранении (имя, должность) =============//
    [
        'name'        => 'companies_access_field_where_call',
        'description' => 'Компании - Номер телефона при отстранении',
    ],
    [
        'name'        => 'companies_access_field_where_call_name',
        'description' => 'Компании - ФИО и должность кому звонить при отстранении',
    ],

    //============== Договоренности с клиентом =============//
    [
        'name'        => 'companies_access_field_note',
        'description' => 'Компании - Редактирование договоренности с клиентом',
    ],
];
