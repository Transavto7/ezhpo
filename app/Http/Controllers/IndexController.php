<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Http\Controllers\Auth\RegisterController;
use App\Imports\CarImport;
use App\Imports\CompanyImport;
use App\Imports\DriverImport;
use App\Point;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as FacadesApp;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;

class IndexController extends Controller
{
    public $elements = [
        // 'ELEMENT_NAME' => USER_ROLE_TYPE (Number)

        /**
         * Для администратора
         */
        'Point' => [
            'title' => 'Пункты выпуска',
            'role' => 777,
            'popupTitle' => 'Пункта выпуска',
            'editOnField' => 'name',

            'model' => 'Point',
            'fields' => [
                'name' => ['label' => 'Пункт выпуска', 'type' => 'text'],
                'pv_id' => ['label' => 'Город', 'type' => 'select', 'values' => 'Town', 'noRequired' => 1],
                'company_id' => ['label' => 'Компания', 'type' => 'select', 'values' => 'Company', 'noRequired' => 1]
            ]
        ],
        'Town' => [
            'title' => 'Города',
            'role' => 777,
            'popupTitle' => 'города',
            'editOnField' => 'name',

            'model' => 'Town',
            'notShowHashId' => 1,
            'fields' => [
                'id' => ['label' => 'ID', 'type' => 'text'],
                'name' => ['label' => 'Город', 'type' => 'text']
            ]
        ],
        'Req' => [
            'title' => 'Реквизиты нашей компании',
            'role' => 777,
            'popupTitle' => 'Реквизитов',
            'editOnField' => 'name',

            'model' => 'Req',
            'fields' => [
                'name' => ['label' => 'Название', 'type' => 'text'],
                'inn' => ['label' => 'ИНН', 'type' => 'number', 'noRequired' => 1],
                'bik' => ['label' => 'БИК', 'type' => 'number', 'noRequired' => 1],
                'kc' => ['label' => 'К/С', 'type' => 'number', 'noRequired' => 1],
                'rc' => ['label' => 'Р/С', 'type' => 'number', 'noRequired' => 1],
                'banks' => ['label' => 'Банки', 'type' => 'text', 'noRequired' => 1],
                'director' => ['label' => 'Должность руководителя', 'type' => 'text', 'noRequired' => 1],
                'director_fio' => ['label' => 'ФИО Руководителя', 'type' => 'text', 'noRequired' => 1],
                'signature' => ['label' => 'Подпись', 'type' => 'file', 'noRequired' => 1],
                'seal' => ['label' => 'Печать', 'type' => 'file', 'noRequired' => 1]
            ]
        ],

        'DDate' => [
            'title' => 'Даты контроля',
            'role' => 777,
            'popupTitle' => 'Даты контроля',
            'editOnField' => 'item_model',

            'model' => 'DDates',
            'fields' => [
                'item_model' => ['label' => 'Сущность', 'type' => 'select', 'values' => ['Driver' => 'Водитель', 'Car' => 'Автомобиль', 'Company' => 'Компания']],
                'field' => ['label' => 'Поле даты проверки', 'type' => 'select', 'values' => [
                    'date_bdd' => 'Дата БДД (водитель)',
                    'date_prmo' => 'Дата ПРМО (водитель)',
                    'date_prto' => 'Дата ПРТО (автомобиль)',
                    'date_report_driver' => 'Дата снятия отчета с карты водителя (водитель)',
                    'date_techview' => 'Дата техосмотра (автомобиль)',
                    'time_skzi' => 'Срок действия СКЗИ (автомобиль)',
                    'time_card_driver' => 'Срок действия карты водителя (водитель)',
                    'date_osago' => 'Дата осаго (автомобиль)'
                ]],
                'days' => ['label' => 'Кол-во дней', 'type' => 'number'],
                'action' => ['label' => 'Действие', 'type' => 'select', 'values' => ['+' => '+', '-' => '-'], 'defaultValue' => '+']
            ]
        ],

        'FieldHistory' => [
            'title' => 'История изменения полей',
            'role' => 777,
            'popupTitle' => 'Истории изменения полей',
            'editOnField' => 'value',

            'model' => 'FieldHistory',
            'fields' => [
                'user_id' => ['label' => 'Пользователь', 'type' => 'select', 'values' => 'User'],
                'value' => ['label' => 'Значение', 'type' => 'text'],
                'field' => ['label' => 'Поле', 'type' => 'text'],
                'created_at' => ['label' => 'Дата', 'type' => 'date'],
            ]
        ],

        'Settings' => [
            'title' => 'Настройки системы',
            'role' => 777,
            'popupTitle' => 'Настройки системы',
            'max' => 1,
            'editOnField' => 'id',

            'model' => 'Settings',
            'fields' => [
                'logo' => ['label' => 'Логотип системы', 'type' => 'file', 'noRequired' => 1],
                'sms_api_key' => ['label' => 'API key sms.ru', 'type' => 'text', 'noRequired' => 1],
                'sms_text_driver' => ['label' => 'Текст SMS для Водителя при непрохождении осмотра', 'type' => 'text', 'noRequired' => 1],
                'sms_text_car' => ['label' => 'Текст SMS для Авто при непрохождении осмотра', 'type' => 'text', 'noRequired' => 1],
                'sms_text_phone' => ['label' => 'Телефон, куда звонить в случае вопросов', 'type' => 'text', 'noRequired' => 1],
                'sms_text_default' => ['label' => 'Текст сообщения по умолчанию', 'type' => 'text', 'noRequired' => 1],
            ]
        ],

        /**
         * Для менеджера
         */
        'Driver' => [
            'title' => 'Водители',
            'role' => 0,
            'popupTitle' => 'Водителя',
            'otherRoles' => ['medic', 'tech'],
            'editOnField' => 'fio',

            'model' => 'Driver',
            'fields' => [
                'old_id' => ['label' => 'Старый ID', 'type' => 'number', 'noRequired' => 1],
                'photo' => ['label' => 'Фото', 'type' => 'file', 'resize' => 1, 'noRequired' => 1],
                'fio' => ['label' => 'ФИО', 'type' => 'text'],
                'year_birthday' => ['label' => 'Дата рождения', 'type' => 'date', 'noRequired' => 1],
                'phone' => ['label' => 'Телефон', 'classes' => 'MASK_PHONE', 'type' => 'text', 'noRequired' => 1],
                'gender' => ['label' => 'Пол', 'type' => 'select', 'values' => ['Мужской' => 'Мужской', 'Женский' => 'Женский'], 'defaultValue' => 'Мужской', 'noRequired' => 1],
                'group_risk' => ['label' => 'Группа риска', 'type' => 'select', 'values' => [
                    'А\Д' => 'А\Д',
                    'Возраст' => 'Возраст',
                    'Алкоголь' => 'Алкоголь',
                    'Наркотики' => 'Наркотики'
                ], 'defaultValue' => 'Не установлено', 'noRequired' => 1],
                'company_id' => ['label' => 'Компания', 'type' => 'select', 'values' => 'Company', 'noRequired' => 1],
                'payment_form' => ['label' => 'Форма оплаты', 'type' => 'select', 'values' => [
                    'Абонентская оплата' => 'Абонентская оплата',
                    'Разовые осмотры' => 'Разовые осмотры'
                ], 'noRequired' => 1],

                'products_id' => ['label' => 'Услуги', 'multiple' => 1, 'type' => 'select', 'values' => 'Product'],

                'count_pl' => ['label' => 'Количество выданных ПЛ', 'type' => 'text', 'noRequired' => 1, 'saveToHistory' => 1],
                'note' => ['label' => 'Примечание', 'type' => 'text', 'noRequired' => 1],
                'procedure_pv' => ['label' => 'Порядок выпуска', 'type' => 'select', 'values' => [
                    'Наперед без дат' => 'Наперед без дат',
                    'Наперёд с датами' => 'Наперёд с датами',
                    'Задним числом' => 'Задним числом',
                    'Фактовый' => 'Фактовый'
                ], 'defaultValue' => 'Фактовый', 'noRequired' => 1],
                'date_bdd' => ['label' => 'Дата БДД', 'type' => 'date', 'noRequired' => 1],
                'date_prmo' => ['label' => 'Дата ПРМО', 'type' => 'date', 'noRequired' => 1],
                'date_report_driver' => ['label' => 'Дата снятия отчета с карты водителя', 'type' => 'date', 'noRequired' => 1],
                'time_card_driver' => ['label' => 'Срок действия карты водителя', 'type' => 'date', 'noRequired' => 1],
                'town_id' => ['label' => 'Город', 'type' => 'select', 'values' => 'Town', 'noRequired' => 1],
                'dismissed' => ['label' => 'Уволен', 'type' => 'select', 'values' => [
                    'Нет' => 'Нет',
                    'Да' => 'Да'
                ]],
                'autosync_fields' => ['label' => 'Автоматическая синхронизация Полей с компанией (по умолч.)', 'type' => 'select', 'values' => [
                    'payment_form' => 'Форма оплаты',
                    'products_id' => 'Услуги'
                ], 'defaultValue' => 'payment_form,products_id', 'multiple' => 1]
            ]
        ],
        'Car' => [
            'title' => 'Автомобили',
            'role' => 0,
            'popupTitle' => 'Автомобиля',
            'otherRoles' => ['medic', 'tech'],
            'editOnField' => 'gos_number',

            'model' => 'Car',
            'fields' => [
                'old_id' => ['label' => 'Старый ID', 'type' => 'number', 'noRequired' => 1],
                'gos_number' => ['label' => 'Гос.номер', 'type' => 'text'],
                'mark_model' => ['label' => 'Марка и модель', 'type' => 'text'],
                'type_auto' => ['label' => 'Тип автомобиля', 'type' => 'select', 'values' => [
                    'В и грузовые автомобили до 3,5 т.' => 'В и грузовые автомобили до 3,5 т.',
                    'С (свыше 3,5 т.)' => 'С (свыше 3,5 т.)',
                    'D' => 'D',
                    'E' => 'E'
                ], 'defaultValue' => 'Не установлено', 'noRequired' => 1],

                'products_id' => ['label' => 'Услуги', 'multiple' => 1, 'type' => 'select', 'values' => 'Product'],

                'trailer' => ['label' => 'Прицеп', 'type' => 'select', 'values' => ['Нет' => 'Нет', 'Да' => 'Да'], 'noRequired' => 1],
                'company_id' => ['label' => 'Компания', 'type' => 'select', 'values' => 'Company', 'noRequired' => 1],
                'payment_form' => ['label' => 'Форма оплаты', 'type' => 'select', 'values' => [
                    'Абонентская оплата' => 'Абонентская оплата',
                    'Разовые осмотры' => 'Разовые осмотры'
                ], 'noRequired' => 1],
                'count_pl' => ['label' => 'Количество выданных ПЛ', 'type' => 'text', 'noRequired' => 1],
                'note' => ['label' => 'Примечание', 'type' => 'text', 'noRequired' => 1],
                'procedure_pv' => ['label' => 'Порядок выпуска', 'type' => 'select', 'values' => [
                    'Наперед без дат' => 'Наперед без дат',
                    'Наперёд с датами' => 'Наперёд с датами',
                    'Задним числом' => 'Задним числом',
                    'Фактовый' => 'Фактовый'
                ], 'defaultValue' => 'Фактовый', 'noRequired' => 1],
                'date_prto' => ['label' => 'Дата ПРТО', 'type' => 'date', 'noRequired' => 1],
                'date_techview' => ['label' => 'Дата техосмотра', 'type' => 'date', 'noRequired' => 1],
                'time_skzi' => ['label' => 'Срок действия СКЗИ', 'type' => 'date', 'noRequired' => 1],
                'date_osago' => ['label' => 'Дата ОСАГО', 'type' => 'date', 'noRequired' => 1],
                'town_id' => ['label' => 'Город', 'type' => 'select', 'values' => 'Town', 'noRequired' => 1],
                'dismissed' => ['label' => 'Уволен', 'type' => 'select', 'values' => [
                    'Нет' => 'Нет',
                    'Да' => 'Да'
                ]],
                'autosync_fields' => ['label' => 'Автоматическая синхронизация Полей с компанией (по умолч.)', 'type' => 'select', 'values' => [
                    'payment_form' => 'Форма оплаты',
                    'products_id' => 'Услуги'
                ], 'defaultValue' => 'payment_form,products_id', 'multiple' => 1]
            ]
        ],
        'Company' => [
            'title' => 'Компании',
            'popupTitle' => 'Компании',
            'role' => 0,
            'editOnField' => 'name',

            'model' => 'Company',
            'fields' => [
                'name' => ['label' => 'Название компании клиента', 'type' => 'text', 'filterJournalLinkKey' => 'company_name'],
                'note' => ['label' => 'Примечание', 'type' => 'text', 'noRequired' => 1],
                'user_id' => ['label' => 'Ответственный', 'type' => 'select', 'values' => 'User', 'noRequired' => 1],
                'req_id' => ['label' => 'Реквизиты нашей компании', 'type' => 'select', 'values' => 'Req', 'noRequired' => 1],
                'pv_id' => ['label' => 'ПВ', 'type' => 'select', 'values' => 'Point', 'noRequired' => 1],
                'town_id' => ['label' => 'Город', 'multiple' => 1, 'type' => 'select', 'values' => 'Town', 'noRequired' => 1, 'syncData' => [
                    ['model' => 'Car', 'fieldFind' => 'company_id', 'text' => 'Автомобиль'],
                    ['model' => 'Driver', 'fieldFind' => 'company_id', 'text' => 'Водитель']
                ]],

                'products_id' => ['label' => 'Услуги', 'multiple' => 1, 'type' => 'select', 'values' => 'Product', 'syncData' => [
                    ['model' => 'Car', 'fieldFind' => 'company_id', 'text' => 'Автомобиль'],
                    ['model' => 'Driver', 'fieldFind' => 'company_id', 'text' => 'Водитель']
                ]],

                'where_call' => ['label' => 'Кому звонить при отстранении', 'classes' => 'MASK_PHONE', 'type' => 'text', 'noRequired' => 1],

                'inn' => ['label' => 'ИНН', 'type' => 'text', 'noRequired' => 1],
                'payment_form' => ['label' => 'Форма оплаты', 'type' => 'select', 'values' => [
                    'Абонентская оплата' => 'Абонентская оплата',
                    'Разовые осмотры' => 'Разовые осмотры'
                ], 'noRequired' => 1, 'syncData' => [
                    ['model' => 'Car', 'fieldFind' => 'company_id', 'text' => 'Автомобиль'],
                    ['model' => 'Driver', 'fieldFind' => 'company_id', 'text' => 'Водитель']
                ]],
                'procedure_pv' => ['label' => 'Порядок выпуска', 'type' => 'select', 'values' => [
                    'Наперед без дат' => 'Наперед без дат',
                    'Наперёд с датами' => 'Наперёд с датами',
                    'Задним числом' => 'Задним числом',
                    'Фактовый' => 'Фактовый'
                ], 'defaultValue' => 'Фактовый', 'noRequired' => 1],

                'dismissed' => ['label' => 'Черный список', 'type' => 'select', 'values' => [
                    'Нет' => 'Нет',
                    'Да' => 'Да'
                ]]
            ]
        ],

        'Discount' => [
            'title' => 'Скидки',
            'role' => 0,
            'popupTitle' => 'Скидка',
            'editOnField' => 'products_id',

            'model' => 'Discount',
            'fields' => [
                'products_id' => ['label' => 'Услуга', 'type' => 'select', 'values' => 'Product'],
                'trigger' => ['label' => 'Триггер (больше/меньше)', 'type' => 'select', 'values' => [
                    '>' => '>',
                    '<' => '<'
                ], 'defaultValue' => '>'],
                'discount' => ['label' => 'Скидка (%)', 'type' => 'porog'],
                'porog' => ['label' => 'Пороговое значение', 'type' => 'number']
            ]
        ],

        'Product' => [
            'title' => 'Услуги',
            'role' => 0,
            'popupTitle' => 'Услуги',
            'editOnField' => 'name',

            'model' => 'Product',
            'fields' => [
                'name' => ['label' => 'Название', 'type' => 'text'],
                'type_product' => ['label' => 'Тип', 'type' => 'select', 'values' => [
                    'Абонентская оплата' => 'Абонентская оплата',
                    'Разовые осмотры' => 'Разовые осмотры'
                ], 'defaultValue' => 'Абонентская оплата'],
                'unit' => ['label' => 'Ед.изм.', 'type' => 'text'],
                'price_unit' => ['label' => 'Стоимость за единицу', 'type' => 'number'],
                'type_anketa' => ['label' => 'Реестр', 'type' => 'select', 'values' => [
                    'bdd' => 'БДД',
                    'medic' => 'Медицинский',
                    'tech' => 'Технический',
                    'Dop' => 'Учет ПЛ',
                    'pechat_pl' => 'Печать ПЛ',
                    'report_cart' => 'Отчеты с карт'
                ], 'defaultValue' => 'Не установлено'],
                'type_view' => ['label' => 'Тип осмотра', 'type' => 'select', 'values' => [
                    'Предрейсовый' => 'Предрейсовый',
                    'Послерейсовый' => 'Послерейсовый',
                    'БДД' => 'БДД',
                    'Отчёты с карт' => 'Отчёты с карт',
                ], 'defaultValue' => 'Не установлено', 'multiple' => 1],
            ]
        ],

        'Instr' => [
            'title' => 'Инструктажи',
            'role' => 0,
            'popupTitle' => 'Инструктажа',
            'editOnField' => 'name',

            'model' => 'Instr',
            'fields' => [
                'photos' => ['label' => 'Фото', 'type' => 'file', 'noRequired' => 1],
                'name' => ['label' => 'Название', 'type' => 'text'],
                'descr' => ['label' => 'Описание', 'type' => 'text'],
                'type_briefing' => ['label' => 'Вид инструктажа', 'type' => 'select', 'values' => [
                    'Вводный' => 'Вводный',
                    'Предрейсовый' => 'Предрейсовый',
                    'Сезонный (осенне-зимний)' => 'Сезонный (осенне-зимний)',
                    'Специальный' => 'Специальный'
                ], 'defaultValue' => 'Вводный'],
                'youtube' => ['label' => 'Ссылка на YouTube', 'type' => 'text'],
                'active' => ['label' => 'Активен', 'type' => 'select', 'values' => [
                    0 => 'Нет',
                    1 => 'Да'
                ], 'defaultValue' => 'Да'],
            ]
        ]


    ];

    public function GetFieldHTML (Request $request)
    {
        $model = $request->model;
        $default_value = !empty($request->default_value) ? $request->default_value : 'Не установлено';
        $field_key = $request->field;

        $field = $this->elements[$model]['fields'][$field_key];

        if($model === 'Point' && $field_key === 'pv_id') {
            $points = Point::getAll();

            return response()->json($points);
        }

        if($field) {
            return view('templates.elements_field', [
                'k' => $field_key,
                'v' => $field,
                'is_required' => '',
                'model' => $model,
                'default_value' => $default_value
            ]);
        }

        return 'Поле не найдено';
    }

    public function syncDataFunc ($data) {
        $model = app("App\\$data[model]");

        if($model) {
            $model = $model->where($data['fieldFind'], $data['fieldFindId']);

            if($data['model'] === 'Driver' || $data['model'] === 'Car') {
                $model->where('autosync_fields', 'LIKE', "%$data[fieldSync]%");
            }

            $model = $model->update([ $data['fieldSync'] => $data['fieldSyncValue'] ]);

            return $model;
        }

        return 0;
    }

    public function SyncDataElement (Request $request)
    {
        $fieldFind = $request->fieldFind;
        $model = $request->model;
        $fieldSync = $request->fieldSync;
        $fieldSyncValue = $request->fieldSyncValue ? $request->fieldSyncValue : '';
        $fieldFindId = $request->fieldFindId;

        $model_text = $model;
        $model = app("App\\$model");

        $is_api = $request->get('api', 0);

        if($model) {
            $model = $this->syncDataFunc([
               'model' => $model_text,
               'fieldFind' => $fieldFind,
               'fieldFindId' => $fieldFindId,
               'fieldSync' => $fieldSync,
               'fieldSyncValue' => $fieldSyncValue
            ]);

            if($model) {
                if(!$is_api) {
                    return view('pages.success', [
                        'text' => "Поля успешно синхронизированы. Кол-во элементов: $model"
                    ]);
                }

                return $model;
            } else {
                if(!$is_api) {
                    return view('pages.warning', [
                        'text' => "Модель $model_text не найдена"
                    ]);
                }

                return 0;
            }
        }

        if($is_api) {
            return 0;
        }

        return abort(500, 'Не найдена модель');
    }

    public function getElements () {
        return $this->elements;
    }

    private $ankets = [
        'medic' => [
            'title' => 'Медицинский осмотр',
            'anketa_view' => 'profile.ankets.medic'
        ],
        'tech' => [
            'title' => 'Технический осмотр',
            'anketa_view' => 'profile.ankets.tech'
        ],
        'Dop' => [
            'title' => 'Журнал ПЛ',
            'anketa_view' => 'profile.ankets.Dop'
        ],

        'pechat_pl' => [
            'title' => 'Журнал печати путевых листов',
            'anketa_view' => 'profile.ankets.pechat_pl'
        ],

        'vid_pl' => [
            'title' => 'Реестр выданных путевых листов',
            'anketa_view' => 'profile.ankets.vid_pl'
        ],

        'bdd' => [
            'title' => 'Журнал инструктажей по БДД',
            'anketa_view' => 'profile.ankets.bdd'
        ],

        'report_cart' => [
            'title' => 'Журнал снятия отчетов с карт',
            'anketa_view' => 'profile.ankets.report_cart'
        ]
    ];

    /**
     * POST-запросы
     */

    public function ImportElements (Request $request)
    {
        $model_type = $request->type;
        $file = $request->file('file');

        $objs = [
            'Company' => CompanyImport::class,
            'Driver' => DriverImport::class,
            'Car' => CarImport::class,
            'Town' => ''
        ];

        if($request->hasFile('file')) {
            //$file = $file->getRealPath();
            //print_r($file);

            $path1 = $request->file('file')->store('temp');
            $path= storage_path('app').'/'.$path1;

            $data = \Maatwebsite\Excel\Facades\Excel::import(new $objs[$model_type], $path);
        }

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function AddElement (Request $request)
    {
        $model_type = $request->type;

        $model = app("App\\$model_type");

        if($model) {
            $data = $request->all();

            unset($data['_token']);

            switch($model_type) {
                case 'Company':

                    $data['hash_id'] = mt_rand(1000,9999) . date('s');

                    break;

                case 'Car':
                    $data['hash_id'] = mt_rand(500000,999999);

                    // СИНХРОНИЗАЦИЯ ПОЛЕЙ
                    if(isset($data['company_id'])) {
                        $fieldsSync = $data['autosync_fields'];

                        if(Company::find($data['company_id'])) {
                            foreach($fieldsSync as $fSync) {
                                $data[$fSync] = Company::find($data['company_id'])->$fSync;
                            }
                        }
                    }

                    break;

                case 'Driver':
                    $data['hash_id'] = mt_rand(100000,499999);

                    $pv_id = isset($data['company_id']) ? Company::where('id', $data['company_id'])->first()->pv_id : 0;

                    $userData = [
                        'hash_id' => $data['hash_id'],
                        'email' => mt_rand(100000,499999) . '@ta-7.ru',
                        'login' => $data['hash_id'],
                        'password' => $data['hash_id'],
                        'name' => $data['fio'],
                        'role' => 3
                    ];

                    if($pv_id) {
                        $userData['pv_id'] = $pv_id;
                    }

                    $register = new RegisterController();
                    $register->create($userData);

                    // СИНХРОНИЗАЦИЯ ПОЛЕЙ
                    if(isset($data['company_id'])) {
                        $fieldsSync = $data['autosync_fields'];

                        if(Company::find($data['company_id'])) {
                            foreach($fieldsSync as $fSync) {
                                $data[$fSync] = Company::find($data['company_id'])->$fSync;
                            }
                        }
                    }

                    break;

                default:
                    $data['hash_id'] = mt_rand(1000,9999) . date('s');
                    break;
            }

            // Парсим файлы
            foreach($request->allFiles() as $file_key => $file) {
                if(isset($data[$file_key]) && !isset($data[$file_key . '_base64'])) {
                    $file_path = Storage::disk('public')->putFile('elements', $file);

                    $data[$file_key] = $file_path;
                }
            }

            // парсим данные
            foreach($data as $dataKey => $dataItem) {
                if(is_array($dataItem)) {
                    if($dataItem !== null) {
                        $data[$dataKey] = join(',', $dataItem);
                    }
                } else if(preg_match('/^data:image\/(\w+);base64,/', $dataItem)) {
                    unset($data[$dataKey]);
                    $dataKey = str_replace('_base64', '', $dataKey);

                    $base64_image = substr($dataItem, strpos($dataItem, ',') + 1);
                    $base64_image = base64_decode($base64_image);

                    $hash = sha1(time());
                    $path = "croppie/$hash.png";
                    $base64_image = Storage::disk('public')->put($path, $base64_image);

                    $data[$dataKey] = $path;
                }
            }

            if($model::create($data)) {
                return redirect( $_SERVER['HTTP_REFERER'] );
            }

        }
    }

    public function RemoveElement (Request $request)
    {
        $model = $request->type;
        $id = $request->id;
        $model = app("App\\$model");

        if($model) {
            if($model::find($id)->delete()) {
                return redirect( $_SERVER['HTTP_REFERER'] );
            }
        }
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
            $data = $request->all();
            $element = $model->find($id);

            unset($data['_token']);

            // Обновляем данные
            if($element) {
                // Парсим файлы
                foreach($request->allFiles() as $file_key => $file) {
                    if(isset($data[$file_key]) && !isset($data[$file_key . '_base64'])) {
                        $file_path = Storage::disk('public')->putFile('elements', $file);

                        Storage::disk('public')->delete($element[$file_key]);
                        $element[$file_key] = $file_path;
                    }
                }

                foreach($data as $k => $v) {
                    if(is_array($v)) {
                        $element[$k] = join(',', $v);
                    }
                    else if(preg_match('/^data:image\/(\w+);base64,/', $v)) {
                        $k = str_replace('_base64', '', $k);

                        $base64_image = substr($v, strpos($v, ',') + 1);
                        $base64_image = base64_decode($base64_image);

                        $hash = sha1(time());
                        $path = "elements/$hash.png";

                        $base64_image = Storage::disk('public')->put($path, $base64_image);

                        $element->$k = $path;
                    }
                    else {
                        if(isset($v) || $v === '') {
                            $element[$k] = $v;
                        }
                    }
                }

                if($model_text === 'Driver' || $model_text === 'Car') {
                    if(isset($element->company_id)) {
                        if($element->company_id) {
                            $aSyncFields = explode(',', $element->autosync_fields);

                            foreach($aSyncFields as $fSync) {
                                $element->$fSync = Company::find($element->company_id)->$fSync;
                            }
                        }
                    }
                } else if ($model_text === 'Company') {

                    if(isset($element->products_id)) {
                        $this->syncDataFunc([
                            'model' => 'Driver',
                            'fieldFind' => 'company_id',
                            'fieldFindId' => $element->id,
                            'fieldSync' => 'products_id',
                            'fieldSyncValue' => $element->products_id
                        ]);

                        $this->syncDataFunc([
                            'model' => 'Car',
                            'fieldFind' => 'company_id',
                            'fieldFindId' => $element->id,
                            'fieldSync' => 'products_id',
                            'fieldSyncValue' => $element->products_id
                        ]);
                    }

                    if(isset($element->payment_form)) {
                        $this->syncDataFunc([
                            'model' => 'Car',
                            'fieldFind' => 'company_id',
                            'fieldFindId' => $element->id,
                            'fieldSync' => 'payment_form',
                            'fieldSyncValue' => $element->payment_form
                        ]);

                        $this->syncDataFunc([
                            'model' => 'Driver',
                            'fieldFind' => 'company_id',
                            'fieldFindId' => $element->id,
                            'fieldSync' => 'payment_form',
                            'fieldSyncValue' => $element->payment_form
                        ]);
                    }

                }

            }

            if($element->save()) {
                return redirect( $_SERVER['HTTP_REFERER'] );
            }
        }

        return abort(500);
    }

    /**
     * Рендеры страниц
     */
    public function RenderIndex (Request $request)
    {
        $user = Auth::user();

        if(!$user) {
            return view('auth.login');
        }

        return redirect()->route('forms');
    }

    /**
     * Рендер элементов для редактирования, добавления и удаления
     */
    public function RenderElements (Request $request)
    {
        $user = Auth::user();
        $type = $request->type;

        /**
         * Сортировка
         */
        $orderKey = $request->get('orderKey', 'created_at');
        $orderBy = $request->get('orderBy', 'DESC');
        $filter = $request->get('filter', 0);

        $take = $request->get('take', 20);

        if(isset($this->elements[$type])) {
            $element = $this->elements[$type];

            $model = $element['model'];
            $MODEL_ELEMENTS = app("App\\$model");
            $element['elements'] = $MODEL_ELEMENTS;
            $fieldsModel = $element['elements']->fillable;

            $element['type'] = $type;
            $element['orderBy'] = $orderBy;
            $element['orderKey'] = $orderKey;
            $element['take'] = $take;

            if($filter) {
                $allFilters = $request->all();
                unset($allFilters['filter']);
                unset($allFilters['take']);
                unset($allFilters['orderBy']);
                unset($allFilters['orderKey']);
                unset($allFilters['page']);

                foreach($allFilters as $aFk => $aFv) {
                    if(!empty($aFv)) {
                        if(is_array($aFv)) {

                            $element['elements'] = $element['elements']->where(function ($q) use ($aFv, $aFk) {

                                foreach($aFv as $aFvItemKey => $aFvItemValue) {
                                    $q = $q->orWhere($aFk, 'LIKE', '%' . $aFvItemValue . '%');
                                }

                                return $q;
                            });

                        } else {
                            $element['elements'] = $element['elements']->where($aFk, 'LIKE', '%' . trim($aFv) . '%');
                        }
                    }
                }
            }

            if(User::getUserCompanyId() && auth()->user()->hasRole('client', '==')) {
                $company_user_id = User::getUserCompanyId();

                if($model == 'Driver' || $model == 'Car') {
                    $element['elements'] = $element['elements']->where('company_id', $company_user_id);
                } else if ($model == 'Company') {
                    $element['elements'] = $element['elements']->where('id', $company_user_id);
                }
            }

            $element['elements_count_all'] = $MODEL_ELEMENTS->all()->count();
            $element['elements'] = $element['elements']->orderBy($orderKey, $orderBy);
            $element['max'] = isset($element['max']) ? $element['max'] : null;

            if($element['max']) {
                $element['elements'] = $element['elements']->take($element['max'])->get();
            } else {
                $element['elements'] = $element['elements']->paginate($take);
            }

            // Проверка прав доступа
            $roles = ['manager', 'admin'];
            if(isset($element['otherRoles'])) {
                foreach($roles as $roleOther) {
                    array_push($element['otherRoles'], $roleOther);
                }
            } else {
                $element['otherRoles'] = $roles;
            }

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

    /**
     * Рендер анкет
     */
    public function RenderForms (Request $request)
    {
        $user = Auth::user();

        if($user->hasRole('client', '==')) {
            return redirect( route('home') );
        }

        $type = $request->get('type', ($user->role === 1 ? 'tech' : 'medic') );
        $company_fields = $this->elements['Driver']['fields']['company_id'];
        $company_fields['getFieldKey'] = 'name';

        $anketa_key = $type;

        // Если пользователь менеджер
        //if($user->role === 11) return redirect(route('home'));

        // Отображаем данные
        $anketa = $this->ankets[$anketa_key];
        $point = Point::getPointText($user->pv_id);
        $points = Point::getAll();

        $time = time();
        // $time += $user->timezone * 3600; (Вывод текущего времени пользователя)
        $time = date('Y-m-d\TH:i', $time);

        // Дефолтные значения
        $anketa['default_current_date'] = $time;
        $anketa['default_point'] = $point;
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
