<?php

namespace App;

use App\Models\Contract;
use App\Models\ContractAnketaSnapshot;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;

class Anketa extends Model
{
    // archive
    public function contract_snapshot()
    {
        return $this->belongsTo(ContractAnketaSnapshot::class, 'contract_snapshot_id', 'id')
                    ->withDefault();
    }


    public function services_snapshot()
    {
        return $this->belongsToMany(
            Product::class,
            'anketa_services_discount_snapshot_contracts',
            'anketa_id',
            'service_id',
            'id',
            'id'
        )->withPivot('service_cost');
    }

    public function our_company()
    {
        return $this->belongsTo(
            Req::class,
            'our_company_id',
            'id'
        )->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
                    ->withDefault();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'hash_id')
                    ->withDefault();
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id', 'hash_id')
                    ->withDefault();
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'hash_id')
                    ->withDefault();
    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }
    protected $casts = [
        'deleted_at' => 'datetime:d-m-Y H:i:s'
    ];

    public $fillable
        = [
            // all
            'id',
            'type_anketa',
            'created_at',
            'deleted_id',
            'deleted_at',
            'is_dop',
            'result_dop',
            'realy',
            'is_pak',
            'flag_pak',

            // tech
            'car_id',
            'car_gos_number',
            'car_mark_model',

            // medic
            'driver_id',
            'driver_fio',
            'driver_group_risk',

            'operator_id',
            'user_id',
            'user_eds',
            'user_name',

            'company_id',
            'company_name',

            'pv_id',
            'point_id',
            'date',
            'number_list_road', //'date_number_list_road',
            'type_view',
            'tonometer',
            'signature',
            't_people',
            'proba_alko',
            'test_narko',
            'med_view',
            'odometer',
            'point_reys_control',
            'admitted',

            // Новые поля
            'driver_gender',
            'driver_year_birthday',
            'complaint',
            'condition_visible_sliz',
            'condition_koj_pokr',

            // Журнал печати ПЛ
            'date_pechat_pl',
            'count_pl',
            'period_pl',
            'added_to_dop',
            'added_to_mo',

            // Журнал БДД
            'type_briefing',
            'briefing_name',

            // ПАК
            'pulse',
            'alcometer_mode',
            'alcometer_result',
            'type_trip',
            'questions',
            'photos',
            'videos',
            'terminal_id',

            //системные поля
            'in_cart',
            'protokol_path',
            'is_medic',
            'closing_path',
            'comments',
            'connected_hash',
            'contract_id',
            'contract_snapshot_id',
        ];


    public static $anketsKeys
        = [
            'medic'       => 'Журнал МО',
            'tech'        => 'Журнал ТО',
            'bdd'         => 'Журнал инструктажей по БДД',
            'report_cart' => 'Реестр снятия отчетов с карт',
            'pechat_pl'   => 'Журнал печати ПЛ',
            //'vid_pl' => 'Реестр выданных путевых листов',
            'pak'         => 'Журнал СДПО',
            'pak_queue'   => 'Очередь на утверждение',
        ];

    public static $fieldsGroupFirst
        = [ // Группа 1 (показываем сразу в HOME)
            'medic' => [
                'date'         => 'Дата осмотра',
                'company_id'   => 'Компания',
                'driver_id'    => 'ID Водителя',
                'period_pl'    => 'Период выдачи ПЛ',
                'realy'        => 'Осмотр реальный?',
                'driver_group_risk'      => 'Группа риска',
                'type_view'    => 'Тип осмотра',
                'proba_alko'   => 'Признаки опьянения', // Проба на алкоголь
                'test_narko'   => 'Тест на наркотики',
                'pv_id'          => 'Пункт выпуска',
            ],
            'tech' => [
                'company_id'     => 'Компания',
                'driver_id'      => 'ID Водителя',
                'car_id'         => 'ID Автомобиля',
                'car_gos_number' => 'Гос. регистрационный номер ТС',
                'date'           => 'Дата осмотра',
                'period_pl'      => 'Период выдачи ПЛ',
                'realy'          => 'Осмотр реальный?',
                'car_type_auto'  => 'Категория ТС',
                'type_view'      => 'Тип осмотра',
                'pv_id'          => 'Пункт выпуска',
                'date_prto'      => 'Дата ПРТО',
            ],
            'bdd' => [
                'company_id'          => 'Компания',
                'driver_id'           => 'Водитель',
                'date'                => 'Дата инструктажа',
                'pv_id'               => 'Пункт выпуска',
            ],
            'pechat_pl' => [
                'company_id'   => 'Компания',
                'driver_id'    => 'Водитель',
                'date'         => 'Дата выдачи',
                'pv_id'        => 'Пункт выпуска',
            ],
            'report_cart' => [
                'company_id' => 'Компания',
                'driver_id'  => 'Водитель',
                'date'       => 'Дата снятия отчета',
                'pv_id'      => 'Пункт выпуска',
            ],
            'pak' => [
                'company_id'     => 'Место работы',
                'driver_id'      => 'Водитель',
                'car_id'         => 'Госномер ТС',
                'date'           => 'Дата осмотра',
                'pv_id'          => 'Пункт выпуска',
                'car_mark_model' => 'Марка/модель автомобиля',
                'flag_pak'       => 'Флаг СДПО',
            ],
            'pak_queue' => [
                'created_at' => 'Дата создания',
                'driver_fio' => 'Водитель',
                'pv_id'      => 'Пункт выпуска',
                'tonometer'  => "Артериальное давление",
                't_people'   => 'Температура тела',
                'proba_alko' => 'Признаки опьянения', // Проба на алкоголь
                'complaint'  => 'Жалобы',
                'admitted'   => 'Заключение о результатах осмотра',
                'photos'     => 'Фото',
                'videos'     => 'Видео',

            ],
        ];

    public static $blockedToExportFields
        = [
            'medic' => [
                // Поля не в выгрузку
                'created_at'        => 'Дата создания',
                'driver_group_risk' => 'Группа риска',
                'company_id'        => 'ID компании',
                'driver_id'         => 'ID водителя',
                'photos'            => 'Фото',
                'med_view'          => 'Мед показания',
                'pv_id'             => 'Пункт выпуска',
                'car_mark_model'    => 'Автомобиль',
                'car_id'            => 'ID автомобиля',
                'number_list_road'  => 'Номер путевого листа',
                'type_view'         => 'Тип осмотра',
                'is_pak'            => 'ПАК',
                'flag_pak'          => 'Флаг СДПО',
                'company_name'      => 'Место работы',
                'realy'             => 'Осмотр реальный?',
                'videos'            => 'Видео',
                'is_dop'            => 'Режим ввода ПЛ',
                'result_dop'        => 'Результат ввода ПЛ',
                'period_pl'         => 'Период ПЛ',
                'signature'         => 'ЭЛ подпись водителя',
            ],
            'tech'  => [
                'realy'        => 'Осмотр реальный?',
            ],
        ];

    public static $fieldsKeysTable = [
        'medic' => [
            'company_id',
            'driver_id',
            'date',
            'period_pl',
            'created_at',
            'driver_group_risk',
            'type_view',
            'realy',
            'proba_alko', // Проба на алкоголь
            'test_narko',
            'pv_id',
            'user_name',
            'driver_gender',
            'driver_year_birthday',
            'complaint',
            'condition_visible_sliz',
            'condition_koj_pokr',
            't_people',
            'tonometer',
            'pulse',
            'admitted',
            'user_eds',
            'photos',
            'videos',
            'med_view',
            'flag_pak',
            'is_dop',
            'operator_id'
        ],
        'tech' => [
            'company_id',
            'driver_id',
            'car_id',
            'date',
            'period_pl',
            'created_at',
            'car_type_auto',
            'type_view',
            'realy',
            'pv_id',
            'odometer',
            'number_list_road',
            'point_reys_control',
            'user_eds',
            'is_dop',
            'user_name',
            'date_prto'
        ],
        'bdd' => [
            'company_id',
            'driver_id',
            'date',
            'type_briefing',
            'created_at',
            'user_name',
            'pv_id',
            'briefing_name',
            'user_eds',
            'signature',
        ],
        'pechat_pl' => [
            'company_id',
            'driver_id',
            'date',
            'count_pl',
            'user_name',
            'pv_id',
            'created_at',
            'user_eds',
        ],
        'report_cart' => [
            'company_id',
            'driver_id',
            'date',
            'user_name',
            'created_at',
            'pv_id',
            'user_eds',
            'signature',
        ]
    ];

    public static $fieldsKeys
        = [ // Группа 2 (скрыты по умолчанию)
            'medic_export_pl' => [
                'date' => 'Дата и время осмотра',
                'driver_fio' => 'ФИО РАБОТНИКА',
                'driver_gender' => 'ПОЛ',
                'driver_year_birthday' => 'ДАТА РОЖДЕНИЯ',
                'complaint' => 'ЖАЛОБЫ',
                'condition_visible_sliz' => 'СОСТОЯНИЕ ВИДИМЫХ СЛИЗИСТЫХ',
                'condition_koj_pokr' => 'СОСТОЯНИЕ КОЖНЫХ ПОКРОВОВ',
                't_people' => 'ТЕМПЕРАТУРА ТЕЛА',
                'tonometer' => 'АРТЕРИАЛЬНОЕ ДАВЛЕНИЕ',
                'pulse' => 'ПУЛЬС',
                'proba_alko' => 'ПРИЗНАКИ ОПЬЯНЕНИЯ',
                'test_narko' => 'Тест на наркотики',
                'admitted' => 'ЗАКЛЮЧЕНИЕ О РЕЗУЛЬТАТАХ ОСМОТРА',
                'user_name' => 'ФИО медицинского работника',
                'user_eds' => 'ЭЦП МЕДИЦИНСКОГО РАБОТНИКА ',
            ],
            'tech_export_to' => [
                'date'               => 'Дата, время проведения контроля',
                'car_mark_model'     => 'Наименование марки, модели ТС',
                'car_gos_number' => 'Гос. регистрационный номер ТС',
                'driver_fio'         => 'ФИО водителя',
                'odometer'           => 'Показания одометра  (полные километры пробега при проведении контроля)',
                'point_reys_control' => 'Отметка о прохождении контроля',
                'user_name'          => 'ФИО лица, проводившего контроль',
                'user_eds'           => 'Подпись лица, проводившего контроль (ЭЦП)',
                'pv_id' => 'Пункт выпуска'
            ],
            'tech_export_pl' => [
                'number_list_road' => 'Номер ПЛ',
                'date'             => 'Дата и время выдачи ПЛ',
                'car_mark_model'   => 'Наименование марки, модели транспортного средства',
                'car_gos_number'   => 'Гос. регистрационный номер ТС',
                'driver_fio'       => 'ФИО водителя',
                'user_name'        => 'ФИО лица, выдавшего ПЛ',
                'user_eds'         => 'Подпись лица, выдавшего ПЛ (ЭЦП)',
            ],
            'bdd_export_prikaz' => [
                'date'          => 'Дата инструктажа',
                'type_briefing' => 'Вид инструктажа',
                'driver_fio'    => 'ФИО водителя',
                'company_name'  => 'Компания',
                'user_name'     => 'ФИО лица, проводившего инструктаж',
                'user_id'       => 'Должность лица, проводившего инструктаж',    // Ну этой хуйни в таблице нет
                'signature'     => 'Подпись водителя, прошедшего инструктаж',
                'user_eds'      => 'Подпись лица, проводившего инструктаж (ЭЦП)',
            ],

            'medic' => [
                // ID
                'company_id'             => 'Компания',
                'driver_id'              => 'ID Водителя',
                'operator_id'            => 'ID оператора',
                //'driver_fio'              => 'ФИО Водителя',

                'date'                   => 'Дата и время осмотра',
                'period_pl'              => 'Период выдачи ПЛ',
                'realy'                  => 'Осмотр реальный?',
                'driver_group_risk'      => 'Группа риска',
                'type_view'              => 'Тип осмотра',
                'proba_alko'             => 'Признаки опьянения', // Проба на алкоголь
                'test_narko'             => 'Тест на наркотики',
                'driver_gender'          => 'Пол',
                'driver_year_birthday'   => 'Дата рождения',
                'complaint'              => 'Жалобы',
                'condition_visible_sliz' => 'Состояние видимых слизистых',
                'condition_koj_pokr'     => 'Состояние кожных покровов',
                't_people'               => 'Температура тела',
                'tonometer'              => "Артериальное давление",
                'pulse'                  => 'Пульс',
                //                'test_narko'             => 'Тест на наркотики',
                'admitted'               => 'Заключение о результатах осмотра',
                'user_name'              => 'ФИО ответственного',
                'user_eds'               => 'ЭЦП медицинского работника',

                // Поля не в выгрузку
                'created_at'             => 'Дата создания',
                'photos'                 => 'Фото',
                'videos'                 => 'Видео',
                'med_view'               => 'Мед показания',
                'pv_id'                  => 'Пункт выпуска',
                //'car_mark_model' => 'Автомобиль',
                //'car_id' => 'ID автомобиля',
                //'number_list_road' => 'Номер путевого листа',
                'flag_pak'               => 'Флаг СДПО',
                'is_dop'                 => 'Режим ввода ПЛ',
            ],
            /**
             * Технический осмотр (параметры полей)
             */
            'tech'      => [
                'company_id'     => 'Компания',
                'driver_id'      => 'Водитель',
                'car_id'         => 'Гос. регистрационный номер ТС',
                'date'           => 'Дата, время проведения контроля',
                'period_pl'      => 'Период выдачи ПЛ',
                'created_at'     => 'Дата создания',
                'realy'          => 'Осмотр реальный?',
                'car_type_auto'  => 'Категория ТС',
                'car_mark_model' => 'Марка автомобиля',
                'type_view'          => 'Тип осмотра',
                'date_prto'      => 'Дата ПРТО',

                // Доп поля

                'number_list_road'   => 'Номер ПЛ',
                //'date_number_list_road' => 'Срок действия путевого листа',
                'odometer'           => 'Показания одометра',
                'point_rseys_control' => 'Отметка о прохождении контроля',
                'user_name'          => 'ФИО ответственного',
                'user_eds'           => 'Подпись лица, проводившего контроль',
                'pv_id'              => 'Пункт выпуска',
                'is_dop'             => 'Режим ввода ПЛ',
            ],
            'bdd' => [
                'company_id'    => 'Компания',
                'driver_id'     => 'Водитель',
                'date'          => 'Дата, время',
                'created_at'    => 'Дата внесения в журнал',
                'type_briefing' => 'Вид инструктажа',
                'user_name'     => 'Ф.И.О (при наличии) лица, проводившего инструктаж',
                'pv_id'         => 'Пункт выпуска',
                'user_eds'      => 'Подпись лица, проводившего инструктаж',
                'signature'     => 'ЭЛ подпись водителя',
            ],
            'pechat_pl' => [
                'company_id'   => 'Компания',
                'driver_id'    => 'Водитель',
                'date'         => 'Дата распечатки ПЛ',
                'count_pl'     => 'Количество распечатанных ПЛ',
                'user_name'    => 'Ф.И.О сотрудника, который готовил ПЛ',
                'user_eds'     => 'ЭЦП сотрудника',
                'pv_id'        => 'Пункт выпуска',
            ],
            /**
             * ЖУРНАЛЫ
             */
            'report_cart' => [
                'company_id'   => 'Компания',
                'driver_id'    => 'Водитель',
                'date'         => 'Дата снятия отчета',
                'user_name'    => 'Ф.И.О (при наличии) лица, проводившего снятие',
                'user_eds'     => 'Подпись лица, проводившего снятие',
                'pv_id'        => 'Пункт выпуска',
                'signature'    => 'ЭЛ подпись водителя',
                'created_at'   => 'Дата/Время создания записи',
            ],
            'pak' => [
                'company_id'             => 'Компания',
                'driver_id'              => 'Водитель',
                'car_id'                 => 'Госномер ТС',
                'date'                   => 'Дата и время осмотра',
                'user_name'              => 'ФИО работника',
                'driver_gender'          => 'Пол',
                'driver_year_birthday'   => 'Дата рождения',
                'complaint'              => 'Жалобы',
                'condition_visible_sliz' => 'Состояние видимых слизистых',
                'condition_koj_pokr'     => 'Состояние кожных покровов',
                't_people'               => 'Температура тела',
                'tonometer'              => "Артериальное давление",
                'pulse'                  => 'Пульс',
                'proba_alko'             => 'Признаки опьянения', // Проба на алкоголь
                'admitted'               => 'Заключение о результатах осмотра',
                'user_eds'               => 'ЭЦП медицинского работника',

                // Поля не в выгрузку
                'created_at'             => 'Дата создания',
                'driver_group_risk'      => 'Группа риска',
                'photos'                 => 'Фото',
                'med_view'               => 'Мед показания',
                'pv_id'                  => 'Пункт выпуска',
                'car_mark_model'         => 'Марка/модель втомобиль',
                'number_list_road'       => 'Номер путевого листа',
                //'date_number_list_road' => 'Срок действия путевого листа',
                'type_view'              => 'Тип осмотра',
                'comments'               => 'Комментарий',
                'flag_pak'               => 'Флаг СДПО',
            ],
            'pak_queue' => [
                'created_at' => 'Дата создания',
                'driver_fio' => 'Водитель',
                'pv_id'      => 'Пункт выпуска',
                'tonometer'  => "Артериальное давление",
                't_people'   => 'Температура тела',
                'proba_alko' => 'Признаки опьянения', // Проба на алкоголь
                'complaint'  => 'Жалобы',
                'admitted'   => 'Заключение о результатах осмотра',
                'photos'     => 'Фото',
                'videos'     => 'Видео',
            ],
        ];

    public function point() {
        return $this->belongsTo(Point::class, 'point_id', 'id');
    }

    public static function getAll()
    {
        return self::all();
    }
}
