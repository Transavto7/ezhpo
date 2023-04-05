<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\FieldPrompt;
use App\Imports\CarImport;
use App\Imports\CompanyImport;
use App\Imports\DriverImport;
use App\Instr;
use App\Models\Contract;
use App\Point;
use App\Product;
use App\Town;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class IndexController extends Controller
{
    public $elements
        = [
            // 'ELEMENT_NAME' => USER_ROLE_TYPE (Number)

            /**
             * Для администратора
             */
            'Town'  => [
                'title'       => 'Города',
                'role'        => 777,
                'popupTitle'  => 'города',
                'editOnField' => 'name',

                'model'         => 'Town',
                'notShowHashId' => 1,
                'fields'        => [
                    'hash_id' => ['label' => 'Город', 'type' => 'select', 'values' => 'Town',
                        'getField' => 'name', 'concatField' => 'hash_id', 'getFieldKey' => 'hash_id'],
                    'name' => ['label' => 'Город', 'type' => 'text', 'hideFilter' => true],
                ],
            ],
            'Point' => [
                'title'       => 'Пункты выпуска',
                'role'        => 777,
                'popupTitle'  => 'Пункта выпуска',
                'editOnField' => 'name',

                'model'  => 'Point',
                'fields' => [
                    'hash_id'    => ['label' => 'Пункт выпуска', 'type' => 'select', 'values' => 'Point',
                        'getField' => 'name', 'concatField' => 'hash_id', 'getFieldKey' => 'hash_id'],
                    'name'    => ['label' => 'Пункт выпуска', 'type' => 'text', 'hideFilter' => true],
                    'pv_id'      => ['label' => 'Город', 'type' => 'select', 'values' => 'Town',
                        'getField' => 'name', 'getFieldKey' => 'id', 'concatField' => 'hash_id'],
                    'company_id' => [
                        'label'      => 'Компания',
                        'type'       => 'select',
                        'values'     => 'Company',
                        'getField' => 'name', 'getFieldKey' => 'id', 'concatField' => 'hash_id',
                        'noRequired' => 1,
                    ],
                ],
            ],
            'Req'   => [
                'title'       => 'Реквизиты нашей компании',
                'role'        => 777,
                'popupTitle'  => 'Реквизитов',
                'editOnField' => 'name',

                'model'  => 'Req',
                'fields' => [
                    'name'         => ['label' => 'Название', 'type' => 'text'],
                    'inn'          => ['label' => 'ИНН', 'type' => 'number', 'noRequired' => 1],
                    'bik'          => ['label' => 'БИК', 'type' => 'number', 'noRequired' => 1],
                    'kc'           => ['label' => 'К/С', 'type' => 'number', 'noRequired' => 1],
                    'rc'           => ['label' => 'Р/С', 'type' => 'number', 'noRequired' => 1],
                    'banks'        => ['label' => 'Банки', 'type' => 'text', 'noRequired' => 1],
                    'director'     => ['label' => 'Должность руководителя', 'type' => 'text', 'noRequired' => 1],
                    'director_fio' => ['label' => 'ФИО Руководителя', 'type' => 'text', 'noRequired' => 1],
                    'seal'         => ['label' => 'Печать', 'type' => 'file', 'noRequired' => 1],
                ],
            ],

            'DDates' => [
                'title'       => 'Даты контроля',
                'role'        => 777,
                'popupTitle'  => 'Даты контроля',
                'editOnField' => 'item_model',

                'model'  => 'DDates',
                'fields' => [
                    'item_model' => [
                        'label'  => 'Сущность',
                        'type'   => 'select',
                        'values' => [
                            'Driver'  => 'Водитель',
                            'Car'     => 'Автомобиль',
                            'Company' => 'Компания',
                        ],
                    ],
                    'field'      => [
                        'label'  => 'Поле даты проверки',
                        'type'   => 'select',
                        'values' => [
                            'date_bdd'            => 'Дата БДД (водитель)',
                            'date_prmo'           => 'Дата ПРМО (водитель)',
                            'date_prto'           => 'Дата ПРТО (автомобиль)',
                            'date_report_driver'  => 'Дата снятия отчета с карты водителя (водитель)',
                            'date_techview'       => 'Дата техосмотра (автомобиль)',
                            'time_skzi'           => 'Срок действия СКЗИ (автомобиль)',
                            'time_card_driver'    => 'Срок действия карты водителя (водитель)',
                            'date_osago'          => 'Дата осаго (автомобиль)',
                            'date_driver_license' => 'Срок действия водительского удостоверения (водитель)',
                        ],
                    ],
                    'days'       => ['label' => 'Кол-во дней', 'type' => 'number'],
                    'action'     => [
                        'label'        => 'Действие',
                        'type'         => 'select',
                        'values'       => ['+' => '+', '-' => '-'],
                        'defaultValue' => '+',
                    ],
                ],
            ],

            'Settings' => [
                'title'       => 'Настройки системы',
                'role'        => 777,
                'popupTitle'  => 'Настройки системы',
                'max'         => 1,
                'editOnField' => 'id',

                'model'  => 'Settings',
                'fields' => [
                    'logo'             => ['label' => 'Логотип системы', 'type' => 'file', 'noRequired' => 1],
                    'sms_api_key'      => ['label' => 'API key sms.ru', 'type' => 'text', 'noRequired' => 1],
                    'sms_text_driver'  => [
                        'label'      => 'Текст SMS для Водителя при непрохождении осмотра',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                    'sms_text_car'     => [
                        'label'      => 'Текст SMS для Авто при непрохождении осмотра',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                    'sms_text_phone'   => [
                        'label'      => 'Телефон, куда звонить в случае вопросов',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                    'sms_text_default' => [
                        'label'      => 'Текст сообщения по умолчанию',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                ],
            ],

            /**
             * Для менеджера
             */
            'Driver'   => [
                'title'       => 'Водители',
                'role'        => 0,
                'popupTitle'  => 'Водителя',
                'otherRoles'  => ['medic', 'tech', 'client'],
                'editOnField' => 'fio',

                'model'  => 'Driver',
                'fields' => [
                    'company_id'    => ['label' => 'Компания', 'type' => 'text', 'values' => 'Company', 'getField' => 'name',
                        'concatField' => 'hash_id', 'getFieldKey' => 'id'],
                    'hash_id'       => [
                        'label' => 'Id водителя',
                        'type' => 'select', 
                        'values' => 'Driver',
                        'getField' => 
                        'fio', 
                        'concatField' => 'hash_id', 
                        'getFieldKey' => 'hash_id'
                    ],
                    'fio' => ['label' => 'ФИО', 'type' => 'text', 'hideFilter' => true],
                    'year_birthday' => ['label' => 'Дата рождения', 'type' => 'date', 'noRequired' => 1],
                    'photo'         => ['label' => 'Фото', 'type' => 'file', 'resize' => 1, 'noRequired' => 1],
                    'phone'         => [
                        'label'      => 'Телефон',
                        'classes'    => 'MASK_PHONE',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                    'gender'        => [
                        'label'        => 'Пол',
                        'type'         => 'select',
                        'values'       => ['Мужской' => 'Мужской', 'Женский' => 'Женский'],
                        'defaultValue' => 'Мужской',
                        'noRequired'   => 1,
                    ],
                    'group_risk'    => [
                        'label'        => 'Группа риска',
                        'type'         => 'select',
                        'values'       => [
                            'Не указано' => 'Не указано',
                            'А\Д'        => 'А\Д',
                            'Возраст'    => 'Возраст',
                            'Алкоголь'   => 'Алкоголь',
                            'Наркотики'  => 'Наркотики',
                        ],
                        'defaultValue' => 'Не указано',
                        'noRequired'   => 1,
                    ],
                    'contract_id'        => [
                        'label'  => 'Договор',
                        'type'   => 'select',
                        'values' => 'Models\Contract',
                    ],
                    /*'products_id'        => [
                        'label'      => 'Услуги[старые]',
                        'multiple'   => 1,
                        'type'       => 'select',
                        'values'     => 'Product',
                        'concatField' => 'hash_id',
                        'noRequired' => 1,
                    ],*/

                    //                'count_pl' => ['label' => 'Количество выданных ПЛ', 'type' => 'text', 'noRequired' => 1, 'saveToHistory' => 1],
                    'note'               => ['label' => 'Примечание', 'type' => 'text', 'noRequired' => 1],
                    'procedure_pv'       => [
                        'label'        => 'Порядок выпуска',
                        'type'         => 'select',
                        'values'       => [
                            'Наперед без дат'  => 'Наперед без дат',
                            'Наперёд с датами' => 'Наперёд с датами',
                            'Задним числом'    => 'Задним числом',
                            'Фактовый'         => 'Фактовый',
                        ],
                        'defaultValue' => 'Фактовый',
                        'noRequired'   => 1,
                    ],
                    'date_bdd'           => ['label' => 'Дата БДД', 'type' => 'date', 'noRequired' => 1],
                    'date_prmo'          => ['label' => 'Дата ПРМО', 'type' => 'date', 'noRequired' => 1],
                    'date_driver_license'=> [
                        'label' => 'Срок действия водительского удостоверения',
                        'type' => 'date',
                        'noRequired' => 1
                    ],
                    'date_narcotic_test'=> [
                        'label' => 'Дата тестирования на наркотики',
                        'type' => 'date',
                        'noRequired' => 1
                    ],
                    'date_report_driver' => [
                        'label'      => 'Дата снятия отчета с карты водителя',
                        'type'       => 'date',
                        'noRequired' => 1,
                    ],
                    'time_card_driver'   => [
                        'label'      => 'Срок действия карты водителя',
                        'type'       => 'date',
                        'noRequired' => 1,
                    ],
                    'town_id'        => [
                        'label'      => 'Город',
                        'type'       => 'select',
                        'values'     => 'Town',
                        'getFieldKey' => 'id',
                        'concatField' => 'hash_id',
                        'noRequired' => 1,
                    ],
                    'dismissed'          => [
                        'label'        => 'Уволен',
                        'type'         => 'select',
                        'values'       => [
                            'Нет' => 'Нет',
                            'Да'  => 'Да',
                        ],
                        'defaultValue' => 'Нет',
                    ],
                    'date_of_employment' => [
                        'label'        => 'Дата устройства на работу',
                        'type'         => 'date',
                        'defaultValue' => 'current_date',
                    ],
                    'autosync_fields'    => [
                        'label'        => 'Автоматическая синхронизация Полей с компанией (по умолч.)',
                        'type'         => 'select',
                        'values'       => [
                            'products_id' => 'Услуги',
                        ],
                        'defaultValue' => 'products_id',
                        'multiple'     => 1,
                        'hidden'       => 1,
                    ],
                    'pressure_systolic' => [
                        'label'        => 'Порог верхнего давления',
                        'type'         => 'number',
                        'noRequired' => 1,
                    ],
                    'pressure_diastolic' => [
                        'label'        => 'Порог нижнего давления',
                        'type'         => 'number',
                        'noRequired' => 1,
                    ],
                ],
            ],
            'Car'      => [
                'title'       => 'Автомобили',
                'role'        => 0,
                'popupTitle'  => 'Автомобиля',
                'otherRoles'  => ['medic', 'tech', 'client'],
                'editOnField' => 'gos_number',

                'model'  => 'Car',
                'fields' => [
                    'company_id' => ['label' => 'Компания', 'type' => 'select', 'values' => 'Company',
                        'getField' => 'name', 'getFieldKey' => 'id', 'concatField' => 'hash_id'],
                    'hash_id'    => ['label' => 'Гос.номер', 'type' => 'select',
                        'values' => 'Car', 'getField' => 'gos_number', 'concatField' => 'hash_id', 'getFieldKey' => 'hash_id'],
                    'gos_number' => ['label' => 'Гос.номер', 'type' => 'text', 'hideFilter' => true],
                    'mark_model' => ['label' => 'Марка и модель', 'type' => 'text'],
                    'type_auto'  => [
                        'label'        => 'Тип автомобиля',
                        'type'         => 'select',
                        'values'       => [
                            'В и грузовые автомобили до 3.5 т.' => 'В и грузовые автомобили до 3.5 т.',
                            'С (свыше 3.5 т.)'                  => 'С (свыше 3.5 т.)',
                            'D'                                 => 'D',
                            'E'                                 => 'E',
                        ],
                        'defaultValue' => 'Не установлено',
                    ],

                    'products_id' => [
                        'label'      => 'Услуги[старые]',
                        'multiple'   => 1,
                        'type'       => 'select',
                        'values'     => 'Product',
                        'concatField' => 'hash_id',
                        'noRequired' => 1,
                    ],

                    'trailer'         => [
                        'label'        => 'Прицеп',
                        'type'         => 'select',
                        'values'       => ['Нет' => 'Нет', 'Да' => 'Да'],
                        'defaultValue' => '',
                        'noRequired'   => 1,
                    ],
                    'contract_id'     => [
                        'label'  => 'Договор',
                        'type'   => 'select',
                        'values' => 'Models\Contract',
                    ],
                    //                'count_pl' => ['label' => 'Количество выданных ПЛ', 'type' => 'text', 'noRequired' => 1],
                    'note'            => ['label' => 'Примечание', 'type' => 'text', 'noRequired' => 1],
                    'procedure_pv'    => [
                        'label'        => 'Порядок выпуска',
                        'type'         => 'select',
                        'values'       => [
                            'Наперед без дат'  => 'Наперед без дат',
                            'Наперёд с датами' => 'Наперёд с датами',
                            'Задним числом'    => 'Задним числом',
                            'Фактовый'         => 'Фактовый',
                        ],
                        'defaultValue' => 'Фактовый',
                        'noRequired'   => 1,
                    ],
                    'date_prto'       => ['label' => 'Дата ПРТО', 'type' => 'date', 'noRequired' => 1],
                    'date_techview'   => ['label' => 'Дата техосмотра', 'type' => 'date', 'noRequired' => 1],
                    'time_skzi'       => ['label' => 'Срок действия СКЗИ', 'type' => 'date', 'noRequired' => 1],
                    'date_osago'      => ['label' => 'Дата ОСАГО', 'type' => 'date', 'noRequired' => 1],
                    'town_id'         => [
                        'label'      => 'Город',
                        'type'       => 'select',
                        'values'     => 'Town',
                        'getFieldKey' => 'id',
                        'concatField' => 'hash_id',
                        'noRequired' => 1,
                    ],
                    'dismissed'       => [
                        'label'        => 'Уволен',
                        'type'         => 'select',
                        'values'       => [
                            'Нет' => 'Нет',
                            'Да'  => 'Да',
                        ],
                        'defaultValue' => 'Нет',
                    ],
                    'autosync_fields' => [
                        'label'        => 'Автоматическая синхронизация Полей с компанией (по умолч.)',
                        'type'         => 'select',
                        'values'       => [
                            'products_id' => 'Услуги',
                        ],
                        'defaultValue' => 'products_id',
                        'multiple'     => 1,
                        'hidden'       => 1,
                    ],
                ],
            ],
            'Company'  => [
                'title'       => 'Компании',
                'popupTitle'  => 'Компании',
                'role'        => 0,
                'editOnField' => 'name',

                'model'  => 'Company',
                'fields' => [
                    'hash_id'    => [
                        'label'                => 'Название компании клиента',
                        'type'                 => 'select',
                        'filterJournalLinkKey' => 'company_id',
                        'values'               => 'Company',
                        'getField'             => 'name',
                        'concatField'          => 'hash_id',
                        'getFieldKey'          => 'hash_id'
                    ],
                    'name' => ['label' => 'Название компании', 'type' => 'text', 'hideFilter' => true],
                    'note'    => ['label' => 'Договоренности с клиентом', 'type' => 'text', 'noRequired' => 1],
                    'comment'    => ['label' => 'Комментарий', 'type' => 'text', 'noRequired' => 1],
                    'procedure_pv' => [
                        'label'        => 'Порядок выпуска',
                        'type'         => 'select',
                        'values'       => [
                            'Наперед без дат'  => 'Наперед без дат',
                            'Наперёд с датами' => 'Наперёд с датами',
                            'Задним числом'    => 'Задним числом',
                            'Фактовый'         => 'Фактовый',
                        ],
                        'defaultValue' => 'Фактовый',
                        'noRequired'   => 1,
                    ],
                    'user_id' => [
                        'label'      => 'Ответственный',
                        'type'       => 'select',
                        'values'     => 'User',
                        'getFieldKey'=> 'id',
                        'concatField' => 'hash_id',
                        'noRequired' => 1,
                    ],
                    'req_id'  => ['label' => 'Реквизиты нашей компании', 'type' => 'select', 'values' => 'Req'],
                    'pv_id'   => ['label' => 'ПВ', 'type' => 'select', 'values' => 'Point',
                        'getFieldKey' => 'id', 'concatField' => 'hash_id', 'noRequired' => 1],
                    'town_id' => [
                        'label'      => 'Город',
                        'multiple'   => 1,
                        'type'       => 'select',
                        'values'     => 'Town',
                        'noRequired' => 1,
                        'getFieldKey' => 'id', 'concatField' => 'hash_id',
                        'syncData'   => [
                            ['model' => 'Car', 'fieldFind' => 'company_id', 'text' => 'Автомобиль'],
                            ['model' => 'Driver', 'fieldFind' => 'company_id', 'text' => 'Водитель'],
                        ],
                    ],
                    'contracts' => [
                        'label'    => 'Договор',
                        'multiple' => 1,
                        'type'     => 'select',
                        'values'   => 'Models\Service',
                    ],

                    'products_id' => [
                        'label'    => 'Услуги [старые]',
                        'multiple' => 1,
                        'noRequired' => 1,
                        'concatField' => 'hash_id',
                        'type'     => 'select',
                        'values'   => 'Product',
                        'syncData' => [
                            ['model' => 'Car', 'fieldFind' => 'company_id', 'text' => 'Автомобиль'],
                            ['model' => 'Driver', 'fieldFind' => 'company_id', 'text' => 'Водитель'],
                        ],
                    ],

                    'where_call'      => [
                        'label'      => 'Номер телефона при отстранении',
                        'classes'    => 'MASK_PHONE',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                    'where_call_name' => [
                        'label'      => 'ФИО и должность кому звонить при отстранении',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],

                    'inn'          => ['label' => 'ИНН', 'type' => 'text'],

                    'dismissed' => [
                        'label'        => 'Временная блокировка',
                        'type'         => 'select',
                        'values'       => [
                            'Нет' => 'Нет',
                            'Да'  => 'Да',
                        ],
                        'defaultValue' => 'Нет',
                    ],

                    'has_actived_prev_month' => [
                        'label'      => 'Были ли активны в прошлом месяце',
                        'type'       => 'select',
                        'values'     => [
                            'Да'  => 'Да',
                            'Нет' => 'Нет',
                        ],
                        'noRequired' => 1,
                    ],

                    'bitrix_link' => [
                        'label'      => 'Ссылка на компанию в Bitrix24',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],

                    'document_bdd' => [
                        'label'      => 'Ссылка на таблицу с документами по бдд',
                        'type'       => 'text',
                        'noRequired' => 1,
                    ],
                    'pressure_systolic' => [
                        'label'        => 'Порог верхнего давления',
                        'type'         => 'number',
                        'noRequired' => 1,
                    ],
                    'pressure_diastolic' => [
                        'label'        => 'Порог нижнего давления',
                        'type'         => 'number',
                        'noRequired' => 1,
                    ],
                ],
            ],
            'Product' => [
                'title'       => 'Услуги',
                'role'        => 0,
                'popupTitle'  => 'Услуги',
                'editOnField' => 'name',

                'model'  => 'Product',
                'fields' => [
                    'hash_id'         => ['label' => 'Услуга', 'type' => 'select',
                        'values' => 'Product', 'getField' => 'name', 'getFieldKey' => 'hash_id', 'concatField' => 'hash_id'],
                    'name' => ['label' => 'Название', 'type' => 'text', 'hideFilter' => true],
                    'type_product' => [
                        'label'        => 'Тип',
                        'type'         => 'select',
                        'values'       => [
                            'Абонентская оплата'             => 'Абонентская оплата',
                            'Разовые осмотры'                => 'Разовые осмотры',
                            'Абонентская плата без реестров' => 'Абонентская плата без реестров',
                        ],
                        'defaultValue' => 'Абонентская оплата',
                    ],
                    'unit'         => ['label' => 'Ед.изм.', 'type' => 'text'],
                    'price_unit'   => ['label' => 'Стоимость за единицу', 'type' => 'number'],
                    'type_anketa'  => [
                        'label'        => 'Реестр',
                        'type'         => 'select',
                        'values'       => [
                            'bdd'         => 'БДД',
                            'medic'       => 'Медицинский',
                            'tech'        => 'Технический',
                            'pechat_pl'   => 'Печать ПЛ',
                            'report_cart' => 'Отчеты с карт',
                        ],
                        'defaultValue' => 'Не установлено',
                    ],
                    'type_view'    => [
                        'label'        => 'Тип осмотра',
                        'type'         => 'select',
                        'values'       => [
                            'Предрейсовый/Предсменный'   => 'Предрейсовый/Предсменный',
                            'Послерейсовый/Послесменный' => 'Послерейсовый/Послесменный',
                            'БДД'                        => 'БДД',
                            'Отчёты с карт'              => 'Отчёты с карт',
                            'Учет ПЛ'                    => 'Учет ПЛ',
                            'Печать ПЛ'                  => 'Печать ПЛ',
                        ],
                        'defaultValue' => 'Не установлено',
                        'multiple'     => 1,
                    ],
                    'essence'      => ['label' => 'Сущности', 'type' => 'text', 'noRequired' => 1],
                ],
            ],
            'Discount' => [
                'title'       => 'Скидки',
                'role'        => 0,
                'popupTitle'  => 'Скидка',
                'editOnField' => 'products_id',

                'model'  => 'Discount',
                'fields' => [
                    'products_id' => ['label' => 'Услуга', 'type' => 'select', 'values' => 'Product',
                        'getField' => 'name', 'getFieldKey' => 'id', 'concatField' => 'hash_id'],
                    'trigger'     => [
                        'label'        => 'Триггер (больше/меньше)',
                        'type'         => 'select',
                        'values'       => [
                            '>' => 'больше',
                            '<' => 'меньше',
                        ],
                        'defaultValue' => '>',
                    ],
                    'porog'       => ['label' => 'Пороговое значение', 'type' => 'number'],
                    'discount'    => ['label' => 'Скидка (%)', 'type' => 'porog'],
                ],
            ],

            'Service' => [
                'title'       => 'Услуги новые',
                'role'        => 0,
                'popupTitle'  => 'Услуги',
                'editOnField' => 'name',

                'model'  => 'Service',
                'fields' => [
                    'name'         => ['label' => 'Название', 'type' => 'text'],
                    'type_product' => [
                        'label'        => 'Тип',
                        'type'         => 'select',
                        'values'       => [
                            'Абонентская оплата'             => 'Абонентская оплата',
                            'Разовые осмотры'                => 'Разовые осмотры',
                            'Абонентская плата без реестров' => 'Абонентская плата без реестров',
                        ],
                        'defaultValue' => 'Абонентская оплата',
                    ],
                    'unit'         => ['label' => 'Ед.изм.', 'type' => 'text'],
                    'price_unit'   => ['label' => 'Стоимость за единицу', 'type' => 'number'],
                    'type_anketa'  => [
                        'label'        => 'Реестр',
                        'type'         => 'select',
                        'values'       => [
                            'bdd'         => 'БДД',
                            'medic'       => 'Медицинский',
                            'tech'        => 'Технический',
                            'pechat_pl'   => 'Печать ПЛ',
                            'report_cart' => 'Отчеты с карт',
                        ],
                        'defaultValue' => 'Не установлено',
                    ],
                    'type_view'    => [
                        'label'        => 'Тип осмотра',
                        'type'         => 'select',
                        'values'       => [
                            'Предрейсовый/Предсменный'   => 'Предрейсовый/Предсменный',
                            'Послерейсовый/Послесменный' => 'Послерейсовый/Послесменный',
                            'БДД'                        => 'БДД',
                            'Отчёты с карт'              => 'Отчёты с карт',
                            'Учет ПЛ'                    => 'Учет ПЛ',
                            'Печать ПЛ'                  => 'Печать ПЛ',
                        ],
                        'defaultValue' => 'Не установлено',
                        'multiple'     => 1,
                    ],
                    'essence'      => ['label' => 'Сущности', 'type' => 'text', 'noRequired' => 1],
                ],
            ],

            'Instr' => [
                'title'       => 'Виды инструктажей',
                'role'        => 0,
                'popupTitle'  => 'Инструктажа',
                'editOnField' => 'name',

                'model'  => 'Instr',
                'fields' => [
                    'photos'        => ['label' => 'Фото', 'type' => 'file', 'noRequired' => 1, 'hideFilter' => true],
                    'hash_id'       => ['label' => 'Инструктаж', 'type' => 'select', 'values' => 'Instr',
                        'getField' => 'name', 'concatField' => 'hash_id', 'getFieldKey' => 'hash_id'],
                    'name' => ['label' => 'Название', 'type' => 'text', 'hideFilter' => true],
                    'descr'         => ['label' => 'Описание', 'type' => 'text'],
                    'type_briefing' => [
                        'label'        => 'Вид инструктажа',
                        'type'         => 'select',
                        'values'       => [
                            'Вводный'                   => 'Вводный',
                            'Предрейсовый'              => 'Предрейсовый',
                            'Сезонный (осенне-зимний)'  => 'Сезонный (осенне-зимний)',
                            'Сезонный (весенне-летний)' => 'Сезонный (весенне-летний)',
                            'Специальный'               => 'Специальный',
                        ],
                        'defaultValue' => 'Вводный',
                    ],
                    'youtube'       => ['label' => 'Ссылка на YouTube', 'type' => 'text'],
                    'active'        => [
                        'label'        => 'Активен',
                        'type'         => 'select',
                        'values'       => [
                            0 => 'Нет',
                            1 => 'Да',
                        ],
                        'defaultValue' => 'Да',
                    ],
                    'sort'          => ['label' => 'Сортировка', 'type' => 'number', 'noRequired' => 1, 'hideFilter' => true],
                    'signature'     => ['label' => 'ЭЛ подпись водителя', 'type' => 'number', 'noRequired' => 1],
                ],
            ],


        ];

    public function GetFieldHTML(Request $request)
    {
        $model         = $request->model;
        $default_value = !empty($request->default_value) ? $request->default_value : 'Не установлено';
        $field_key     = $request->field;

        $field = $this->elements[$model]['fields'][$field_key];

        if ($model === 'Point' && $field_key === 'pv_id') {
            $points = Point::getAll();

            return response()->json($points);
        }

        if ($field) {
            return view('templates.elements_field', [
                'k'             => $field_key,
                'v'             => $field,
                'is_required'   => '',
                'model'         => $model,
                'default_value' => $default_value,
            ]);
        }

        return 'Поле не найдено';
    }

    public function syncDataFunc($data)
    {
        $model = app("App\\$data[model]");

        if ($model) {
            $model = $model->where($data['fieldFind'], $data['fieldFindId']);

            if ($data['model'] === 'Driver' || $data['model'] === 'Car') {
                $model->where('autosync_fields', 'LIKE', "%$data[fieldSync]%");
            }

            $model = $model->update([$data['fieldSync'] => $data['fieldSyncValue']]);

            return $model;
        }

        return 0;
    }

    public function SyncDataElement(Request $request)
    {
        $fieldFind      = $request->fieldFind;
        $model          = $request->model;
        $fieldSync      = $request->fieldSync;
        $fieldSyncValue = $request->fieldSyncValue ? $request->fieldSyncValue : '';
        $fieldFindId    = $request->fieldFindId;

        $model_text = $model;
        $model      = app("App\\$model");

        $is_api = $request->get('api', 0);

        if ($model) {
            $model = $this->syncDataFunc([
                                             'model'          => $model_text,
                                             'fieldFind'      => $fieldFind,
                                             'fieldFindId'    => $fieldFindId,
                                             'fieldSync'      => $fieldSync,
                                             'fieldSyncValue' => $fieldSyncValue,
                                         ]);

            if ($model) {
                if ( !$is_api) {
                    return view('pages.success', [
                        'text' => "Поля успешно синхронизированы. Кол-во элементов: $model",
                    ]);
                }

                return $model;
            } else {
                if ( !$is_api) {
                    return view('pages.warning', [
                        'text' => "Модель $model_text не найдена",
                    ]);
                }

                return 0;
            }
        }

        if ($is_api) {
            return 0;
        }

        return abort(500, 'Не найдена модель');
    }

    public function getElements()
    {
        return $this->elements;
    }

    private $ankets
        = [
            'medic' => [
                'title'       => 'Медицинский осмотр',
                'anketa_view' => 'profile.ankets.medic',
            ],
            'tech'  => [
                'title'       => 'Технический осмотр',
                'anketa_view' => 'profile.ankets.tech',
            ],

            'pechat_pl' => [
                'title'       => 'Журнал печати путевых листов',
                'anketa_view' => 'profile.ankets.pechat_pl',
            ],

            'pak' => [
                'title'       => 'СДПО',
                'anketa_view' => 'profile.ankets.pak',
            ],

            'pak_queue' => [
                'title'       => 'Очередь на утверждение',
                'anketa_view' => 'profile.ankets.pak_queue',
            ],

            'vid_pl' => [
                'title'       => 'Реестр выданных путевых листов',
                'anketa_view' => 'profile.ankets.vid_pl',
            ],

            'bdd' => [
                'title'       => 'Журнал инструктажей по БДД',
                'anketa_view' => 'profile.ankets.bdd',
            ],

            'report_cart' => [
                'title'       => 'Журнал снятия отчетов с карт',
                'anketa_view' => 'profile.ankets.report_cart',
            ],
        ];

    /**
     * POST-запросы
     */

    public function ImportElements(Request $request)
    {
        $model_type = $request->type;
        $file       = $request->file('file');

        $objs = [
            'Company' => CompanyImport::class,
            'Driver'  => DriverImport::class,
            'Car'     => CarImport::class,
            'Town'    => '',
        ];

        if ($request->hasFile('file')) {
            //$file = $file->getRealPath();
            //print_r($file);

            $path1 = $request->file('file')->store('temp');
            $path  = storage_path('app').'/'.$path1;

            $data = \Maatwebsite\Excel\Facades\Excel::import(new $objs[$model_type], $path);
        }

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function AddElement(Request $request)
    {
        $model_type = $request->type;

        $model = app("App\\$model_type");

        if ($model) {
            $data = $request->all();

            unset($data['_token']);

            if ($request->user()->hasRole('client')) {
                $data['company_id'] = $request->user()->company_id;
            }

            switch ($model_type) {
                case 'Company':

                    /**
                     * <Проврка на дубликат по НАЗВАНИЮ/НОМЕРУ>
                     */
                    if (isset($data['name'])) {
                        $elDouble = Company::where('name', trim($data['name']))->count();

                        if ($elDouble > 0) {
                            return back()->withErrors([
                                                          'errors' => 'Найден дубликат по названию компании',
                                                      ]);
                        }
                    }
                    /**
                     * </Проверка на дубликат по названию/номеру>
                     */

                    $data['hash_id'] = mt_rand(1000, 9999).date('s');
                    break;

                case 'Car':
                    $data['hash_id'] = mt_rand(500000, 999999);

                    // СИНХРОНИЗАЦИЯ ПОЛЕЙ
                    if (isset($data['company_id'])) {
                        $fieldsSync = isset($data['autosync_fields']) ? $data['autosync_fields'] : [];

                        /**
                         * <Проврка на дубликат по НАЗВАНИЮ/НОМЕРУ>
                         */
                        if (isset($data['gos_number'])) {
                            $elDouble = Car::where('company_id', $data['company_id'])
                                           ->where('gos_number', trim($data['gos_number']))->count();

                            if ($elDouble > 0) {
                                return back()->withErrors([
                                                              'errors' => 'Найден дубликат по гос.номеру Автомобиля',
                                                          ]);
                            }
                        }
                        /**
                         * </Проверка на дубликат по названию/номеру>
                         */

                        if (Company::find($data['company_id'])) {
                            foreach (['products_id'] as $fSync) {
                                $fsyncData = Company::find($data['company_id'])->$fSync;

                                if ($fsyncData) {
                                    $data[$fSync] = $fsyncData;
                                }
                            }
                        }
                    }

                    break;

                case 'Driver':
                    $data['hash_id'] = mt_rand(100000, 499999);

                    $pv_id = isset($data['company_id']) ? Company::where('id', $data['company_id'])->first()->pv_id : 0;

                    /**
                     * <Проврка на дубликат по НАЗВАНИЮ/НОМЕРУ>
                     */
                    if (isset($data['company_id']) && isset($data['fio'])) {
                        $elDouble = Driver::where('company_id', $data['company_id'])->where('fio', trim($data['fio']))
                                          ->count();

                        if ($elDouble > 0) {
                            return back()->withErrors([
                                                          'errors' => 'Найден дубликат по ФИО Водителя',
                                                      ]);
                        }
                    }
                    /**
                     * </Проверка на дубликат по названию/номеру>
                     */

                    $userData = [
                        'hash_id'  => $data['hash_id'],
                        'api_token' => Hash::make(date('H:i:s').sha1($data['hash_id'])),
                        'email'    => mt_rand(100000, 499999).'@ta-7.ru',
                        'login'    => $data['hash_id'],
                        'password' => Hash::make($data['hash_id']),
                        'name'     => $data['fio'],
                        'role'     => 3,
                    ];

                    if ($pv_id) {
                        $userData['pv_id'] = $pv_id;
                    }

                    $user = User::create($userData);
                    $user->roles()->attach(3);

                    // СИНХРОНИЗАЦИЯ ПОЛЕЙ
                    if (isset($data['company_id'])) {
                        $fieldsSync = isset($data['autosync_fields']) ? $data['autosync_fields'] : [];

                        if (Company::find($data['company_id'])) {
                            foreach (['products_id'] as $fSync) {
                                $fsyncData = Company::find($data['company_id'])->$fSync;

                                if ($fsyncData) {
                                    $data[$fSync] = $fsyncData;
                                }
                            }
                        }
                    }

                    break;
                case 'Product':
                    $data['hash_id'] = mt_rand(100000, 499999);
                    if ($data['type_product'] === 'Абонентская плата без реестров') {
                        $data['type_anketa'] = null;
                        $data['type_view']   = null;
                    }

                    break;
                default:
                    $data['hash_id'] = mt_rand(1000, 9999).date('s');
                    break;
            }

            // Парсим файлы
            foreach ($request->allFiles() as $file_key => $file) {
                if (isset($data[$file_key]) && !isset($data[$file_key.'_base64'])) {
                    $file_path = Storage::disk('public')->putFile('elements', $file);

                    $data[$file_key] = $file_path;
                }
            }

            // парсим данные
            foreach ($data as $dataKey => $dataItem) {
                if (is_array($dataItem)) {
                    if ($dataKey['contracts'] ?? false) {
                        $contracts = $data['contracts'] ?? [];
                        unset($data['contracts']);
                        continue;
                    }
                    if ($dataItem !== null) {
                        $data[$dataKey] = join(',', $dataItem);

                    }
                } else {
                    if (preg_match('/^data:image\/(\w+);base64,/', $dataItem)) {
                        unset($data[$dataKey]);
                        $dataKey = str_replace('_base64', '', $dataKey);

                        $base64_image = substr($dataItem, strpos($dataItem, ',') + 1);
                        $base64_image = base64_decode($base64_image);

                        $hash         = sha1(time());
                        $path         = "croppie/$hash.png";
                        $base64_image = Storage::disk('public')->put($path, $base64_image);

                        $data[$dataKey] = $path;
                    } else {
                        $data[$dataKey] = $dataItem ? trim($dataItem) : $dataItem;
                    }
                }
            }

            $created = $model::create($data);


            if ($model_type === 'Driver') {
                $company = Company::where("id", $data['company_id'])->first();
                $briefing = Instr::where('is_default', true)->where('type_briefing', 'Вводный')->first();

                if ($company && $company->required_type_briefing) {
                    $bddUser = User::with(['roles'])->whereHas('roles', function ($queryBuilder) {
                        return $queryBuilder->where('id', 7);
                    })->get()->random();
                    $point = Point::find($company->pv_id);

                    Anketa::create([
                        "type_anketa" => "bdd",
                        "user_id"     => $bddUser->id,
                        "user_name"   => $bddUser->name,
                        "driver_id"   => $created->hash_id,
                        "driver_fio"  => $created->fio,
                        "driver_gender" => $created->gender,
                        'pv_id' => $point ? $point->name : null,
                        'point_id' => $point ? $point->id : null,
                        'user_eds' => $bddUser->eds,
                        "driver_year_birthday" => $created->year_birthday,
                        "complaint" => "Нет",
                        "type_briefing" => 'Вводный',
                        "signature" => "Подписано простой ЭЦП",
                        "condition_visible_sliz" => "Без особенностей",
                        "condition_koj_pokr" => "Без особенностей",
                        "date" => Carbon::now(),
                        "type_view" => "Предрейсовый",
                        "company_id" => $company->hash_id,
                        "company_name" => $company->name,
                        "briefing_name" => $briefing->name ?? '',
                        'realy' => 'Нет'
                    ]);
                }
            }


            if ($created) {
                if ($model_type === 'Company') {
                    $user = User::create([
                         'hash_id'  => mt_rand(0,9999) . date('s'),
                         'email'    => $created->hash_id . '-' . mt_rand(100000, 499999).'@ta-7.ru',
                         'api_token' => Hash::make(date('H:i:s').sha1($created->hash_id)),
                         'login'    => '0' . $created->hash_id,
                         'password' => Hash::make('0' .$created->hash_id),
                         'name'     => $created->name,
                         'role'     => 12,
                         'company_id' => $created->id
                     ]);

                    $user->roles()->attach(6);
                }else if($model_type === 'Car' || $model_type === 'Driver'){
                    if($created->company_id){
                        $contracts_ids = Contract::where('company_id', $created->company_id)
                                                 ->forDate(Carbon::now())
                                                 ->main()
                                                 ->get(['id']);

                        $created->contracts()->sync($contracts_ids->pluck('id'));
                    }
                }

                return redirect($_SERVER['HTTP_REFERER']);
            }

        }
    }

    public function RemoveElement (Request $request)
    {
        $model = $request->type;
        $id = $request->id;
        $model = app("App\\$model");

        if ($model) {
//            if($model instanceof Company){
//                Car::where('company_id', $model->id)->update(['contract_id' => null]);
//                Driver::where('company_id', $model->id)->update(['contract_id' => null]);
//            }

            if ($request->get('undo')) {
                $model::withTrashed()->find($id)->restore();

                return redirect($_SERVER['HTTP_REFERER']);
            }
            if ($model::find($id)->delete()) {
                return redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    // Для компаний синхронизация
    // Ставит услуги машинам и водителям такие же, как и у компании
    public function syncElement(Request $request)
    {
        if ($request->type !== 'Company') {
            return redirect($_SERVER['HTTP_REFERER']);
        }
        $id      = $request->id;
        $company = Company::find($id);

        if(!$company->products_id){
            return redirect($_SERVER['HTTP_REFERER']);
        }

        Car::where('company_id', $id)->update(['products_id' => $company->products_id]);
        Driver::where('company_id', $id)->update(['products_id' => $company->products_id]);


        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function DeleteFileElement (Request $request)
    {
        $id = $request->id;
        $field = $request->field;
        $model_text = $request->model;

        $model = app("App\\$model_text");

        if($model) {
            $model = $model->find($id);

            Storage::disk('public')->delete($model->$field);

            $model->$field = '';
            $model->save();
        }

        return redirect( $_SERVER['HTTP_REFERER'] );
    }

    public function UpdateElement (Request $request)
    {
        $model_text = $request->type;
        $model = app("App\\$model_text");
        $id = $request->id;

        if($model) {
            $data         = $request->all();
            $oldDataModel = [];
            $element      = $model->find($id);

            unset($data['_token']);

            if ($request->user()->hasRole('client')) {
                $data['company_id'] = $request->user()->company_id;
            }

            // Обновляем данные
            if ($element) {
                // Парсим файлы
                foreach ($request->allFiles() as $file_key => $file) {
                    if (isset($data[$file_key]) && !isset($data[$file_key.'_base64'])) {
                        Storage::disk('public')->delete($element[$file_key]);

                        $file_path = Storage::disk('public')->putFile('elements', $file);

                        $element[$file_key] = $file_path;
                    }
                }

                foreach ($data as $k => $v) {
                    $oldDataModel[$k] = $element[$k];

                    if ($k === 'contracts' || $k === 'contract_id' || $k === 'contract_ids') {
                        continue;
                    }

                    if (is_array($v)) {
                        $element[$k] = join(',', $v);
                    } else {
                        if (preg_match('/^data:image\/(\w+);base64,/', $v)) {
                            $k = str_replace('_base64', '', $k);

                            $base64_image = substr($v, strpos($v, ',') + 1);
                            $base64_image = base64_decode($base64_image);

                            $hash = sha1(time());
                            $path = "elements/$hash.png";

                            $base64_image = Storage::disk('public')->put($path, $base64_image);

                            $element->$k = $path;
                        } else {
                            if (isset($v) && !$request->hasFile($k)) {
                                $element[$k] = $v;
                            } else if ($k === 'pressure_systolic' || $k === 'pressure_diastolic') {
                                $element[$k] = null;
                            }
                        }
                    }
                }


                //АВТО <- КОМПАНИЯ (СИНХРОНИЗАЦИЯ ПОЛЕЙ)
                if ($model_text === 'Driver' || $model_text === 'Car') {
                    if (isset($element->company_id)) {
                        if ($element->company_id) {
                            if ($oldDataModel['company_id'] != $element->company_id) {
                                $aSyncFields = explode(',', $element->autosync_fields);

                                foreach ($aSyncFields as $fSync) {
                                    $company = Company::find($element->company_id);
                                    if ($company) {
                                        $element->$fSync = $company->$fSync;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($model_text === 'Product' || $model_text === 'Service') {
                    if ($element->type_product === 'Абонентская плата без реестров') {
                        $element->type_anketa = null;
                        $element->type_view   = null;

                        if ( !isset($data['essence'])) {
//                            $element->essence = null;
                        }
                    } else {
//                        $element->essence = null;
                    }
                }

                if ($model_text === 'Company') {

                    if (isset($element->products_id)) {
                        $this->syncDataFunc([
                            'model'          => 'Driver',
                            'fieldFind'      => 'company_id',
                            'fieldFindId'    => $element->id,
                            'fieldSync'      => 'products_id',
                            'fieldSyncValue' => $element->products_id
                        ]);

                        $this->syncDataFunc([
                            'model'          => 'Car',
                            'fieldFind'      => 'company_id',
                            'fieldFindId'    => $element->id,
                            'fieldSync'      => 'products_id',
                            'fieldSyncValue' => $element->products_id
                        ]);
                    }

                    $requiredBriefing = isset($data['required_type_briefing']) && $data['required_type_briefing'] == 'on';
                    $element->required_type_briefing = $requiredBriefing;
                }

            }

            if ($model_text === 'Instr') {
                $isDefault = isset($data['is_default']) && $data['is_default'] == 'on';
                Instr::where('type_briefing', $element->type_briefing)->where('is_default', 1)->update(["is_default" => 0]);
                $element->is_default = $isDefault;
            }

            /**
             * Пустые поля обновляем (На самом деле нет)
             */
            foreach ($oldDataModel as $oldDataItemKey => $oldDataItemValue) {
                if ( !isset($data[$oldDataItemKey])
                     && ($oldDataItemKey == 'note'
                         || $oldDataItemKey == 'document_bdd')) {
                    $element[$oldDataItemKey] = '';
                }
            }
        }

        if($element->save()){
            if (($model_text == 'Driver' && user()->access('contract_edit_driver'))
                || ($model_text == 'Car' && user()->access('contract_edit_car'))){
                $element->contracts()->sync($data['contract_ids'] ?? []);
            }
        }


        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function showEditModal($model, $id)
    {
        $page = $this->elements[$model];

        $page['model'] = $model;
        $page['id']    = $id;


        if($model == 'Company' ||
           $model == 'Driver' ||
           $model == 'Car'){
            $el = app("App\\$model")
                ->with(['contracts.services'])
                ->find($id);
        }else{
            $el = app("App\\$model")->find($id);
        }

        $page['el'] = $el;

        echo view('showEditElementModal', $page);
    }

    /**
     * Рендеры страниц
     */
    public function RenderIndex(Request $request)
    {
        $user = Auth::user();

        if ( !$user) {
            return view('auth.login');
        }

        return redirect()->route('forms');
    }

    /**
     * Рендер просмотра вкладок CRM
     */
    public function RenderElements (Request $request)
    {
        $user = Auth::user();
        $type = $request->type;

        $queryString = '';
        $oKey = 'orderKey';
        $oBy = 'orderBy';

        // ОПЕРАТОР ПАК & КОМПАНИИ

        foreach($_GET as $getK => $getV) {
            if($getK !== $oKey && $getK !== $oBy) {
                if(is_array($getV)) {
                    foreach($getV as $getVkey => $getVvalue) {
                        $queryString .= '&' . $getK . "[$getVkey]" . '=' . $getVvalue;
                    }
                } else {
                    $queryString .= '&' . $getK . '=' . $getV;
                }

            }
        }

        /**
         * Сортировка
         */
        $orderKey = $request->get($oKey, 'created_at');
        $orderBy = $request->get($oBy, 'DESC');
        $filter = $request->get('filter', 0);

        $take = $request->get('take', 500);

        if(isset($this->elements[$type])) {
            $element = $this->elements[$type];

            $model = $element['model'];
            $MODEL_ELEMENTS = app("App\\$model");

            $element['elements_count_all'] = $MODEL_ELEMENTS->count();

            if ($model == 'Company') {
                $MODEL_ELEMENTS = $MODEL_ELEMENTS->with(['contracts.services']);
            } elseif ($model == 'Car' || $model == 'Driver') {
                $MODEL_ELEMENTS = $MODEL_ELEMENTS->with(['contracts.services']);
            }

            $element['elements'] = $MODEL_ELEMENTS;

            $element['type'] = $type;
            $element['orderBy'] = $orderBy;
            $element['orderKey'] = $orderKey;
            $element['take'] = $take;

            if($request->get('deleted')){
                $element['elements'] = $element['elements']->onlyTrashed();
            }
            if($filter) {
                $allFilters = $request->all();
                unset($allFilters['filter']);
                unset($allFilters['take']);
                unset($allFilters['orderBy']);
                unset($allFilters['orderKey']);
                unset($allFilters['page']);
                unset($allFilters['deleted']);

                foreach($allFilters as $aFk => $aFv) {
                    if(!empty($aFv)) {
                        if(is_array($aFv)) {
                            if(count($aFv) > 0) {
                                $element['elements'] = $element['elements']->where(function ($q) use ($aFv, $aFk) {
                                    $isId = strpos($aFk, '_id');

                                    foreach($aFv as $aFvItemKey => $aFvItemValue) {
                                        if ($isId && ($aFk === 'town_id' || $aFk === 'products_id')) {
                                            $q = $q->orWhere($aFk, $aFvItemValue)
                                                   ->orWhere($aFk, 'like', "%,$aFvItemValue,%")
                                                   ->orWhere($aFk, 'like', "%,$aFvItemValue")
                                                   ->orWhere($aFk, 'like', "$aFvItemValue,%");
                                        } else {
                                            if ($isId) {
                                                $q = $q->orWhere($aFk, $aFvItemValue);
                                            } else {
                                                if (strlen($aFvItemValue) === 0) {
                                                    $q = $q->orWhere($aFk, $aFvItemValue);
                                                } else {
                                                    $q = $q->orWhere($aFk, 'LIKE', '%'.trim($aFvItemValue).'%');
                                                }
                                            }
                                        }
                                    }

                                    return $q;
                                });
                            }
                        } else {
                            if ($aFk == 'date_of_employment') {
                                $element['elements'] = $element['elements']->whereBetween($aFk, [
                                    Carbon::parse($aFv)->startOfDay(),
                                    Carbon::parse($aFv)->endOfDay()
                                ]);
                            } else {
                                $element['elements'] = $element['elements']->where($aFk, 'LIKE', '%' . trim($aFv) . '%');
                            }
                        }
                    }
                }
            }

            if(auth()->user()->hasRole('client')) {
                if($model == 'Driver' || $model == 'Car' || $model == 'Company') {
                    $element['elements'] = $element['elements']->where('company_id', auth()->user()->company_id);
                } else {
                    $element['elements'] = $element['elements']->where('id', auth()->user()->company_id);
                }
            }


            $element['max'] = isset($element['max']) ? $element['max'] : null;
            $element['elements_count_all'] = $MODEL_ELEMENTS->count();
            $element['elements'] = $element['elements']->orderBy($orderKey, $orderBy);

            // Автоматическая загрузка справочников
            $excludeElementTypes = [
                'Settings',
                'Discount',
                'DDates',
                'DDate',
                'Product',
                'Instr',
                'Town',
                'Point',
                'Req',
            ];


            if ($filter || in_array($type, $excludeElementTypes)
                || ($user->hasRole('client')
                    && ($type === 'Driver'
                        || $type === 'Car'))) {
                if ($element['max']) {
                    $element['elements'] = $element['elements']->take($element['max'])->get();
                } else {
                    $element['elements'] = $element['elements']->paginate($take);
                }
            } else {
                $element['elements'] = [];
            }

            // Проверка прав доступа
            $roles = ['manager', 'admin'];
            if (isset($element['otherRoles'])) {
                foreach ($roles as $roleOther) {
                    array_push($element['otherRoles'], $roleOther);
                }
            } else {
                $element['otherRoles'] = $roles;
            }

            $element['queryString'] = $queryString;
            $fieldsQuery = FieldPrompt::where('type', strtolower($model));
            $element['fieldPrompts'] = $fieldsQuery->get();
            return view('elements', $element);
        } else {
            return redirect( route('home') );
        }
    }

    /**
     * Рендер последовательного добавления Клиента
     */
    public function RenderAddClient (Request $request)
    {
        return view('pages.add_client', [
            'title' => 'Добавление клиента'
        ]);
    }

    public function RenderHome()
    {
        return view('index');
    }

    /**
     * Рендер анкет
     */
    public function RenderForms (Request $request)
    {
        $user = Auth::user();

        if(!($type = $request->get('type'))){
            if(user()->hasRole('tech')){
                $type = 'tech';
            }
            if(user()->hasRole('medic')){
                $type = 'medic';
            }
            if(user()->hasRole('manager') || user()->hasRole('engineer_bdd')){
                return redirect()->route('renderElements', 'Company');
            }
            if(user()->hasRole('operator_sdpo')){
                return redirect()->route('home', 'pak_queue');
            }

            if(user()->hasRole('client')){
                return redirect()->route('home', ['type_ankets' => 'medic']);
            }
            if(!$type){
                return redirect()->route('index');
            }
        }

        $company_fields = $this->elements['Driver']['fields']['company_id'];
        $company_fields['getFieldKey'] = 'name';

        $anketa_key = $type;

        // Отображаем данные
        $anketa = $this->ankets[$anketa_key];
        $points = Point::getAll();

        // Конвертация текущего времени Юзера
        date_default_timezone_set('UTC');

        $time = time();

        $timezone = $user->timezone ? $user->timezone : 3;

        $time += $timezone * 3600;
        $time = date('Y-m-d\TH:i', $time);

        // Дефолтные значения
        $anketa['default_current_date'] = $time;
        $anketa['points'] = $points;
        $anketa['type_anketa'] = $anketa_key;
        $anketa['default_pv_id'] = $user->pv_id;
        $anketa['company_fields'] = $company_fields;

        $anketa['Driver'] = Driver::class;
        $anketa['Car'] = Car::class;

        // Проверяем выставленный ПВ
        if(session()->exists('anketa_pv_id')) {
            $session_pv_id = session('anketa_pv_id');

            if(date('d.m') > $session_pv_id['expired']) {
                session()->remove('anketa_pv_id');
            }
        }

        return view('profile.anketa', $anketa);
    }
}
