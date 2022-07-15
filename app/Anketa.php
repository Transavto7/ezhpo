<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anketa extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'hash_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id', 'hash_id');
    }


    public $fillable
        = [
            'id',
            'type_anketa',

            'car_id',
            'car_gos_number',
            'car_mark_model',

            'driver_id',
            'driver_fio',
            'driver_group_risk',

            'user_id',
            'user_eds',
            'user_name',

            'company_id',
            'company_name',

            'pv_id',
            'date',
            'created_at',
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
            'is_dop',
            'result_dop',

            // Журнал печати ПЛ
            'date_pechat_pl',
            'count_pl',
            'period_pl',
            'added_to_dop',
            'added_to_mo',
            'realy',

            // Журнал БДД
            'type_briefing',

            // ПАК
            'pulse',
            'alcometer_mode',
            'alcometer_result',
            'type_trip',
            'questions',
            'photos',
            'videos',

            //системные поля
            'in_cart',
            'is_pak',
            'protokol_path',
            'comments',
            'flag_pak',
            'connected_hash',
        ];

    public static $anketsKeys
        = [
            'Dop'         => 'Журнал учёта путевых листов',
            'medic'       => 'Журнал предрейсовых медицинских осмотров',
            'tech'        => 'Журнал предрейсовых технических осмотров',
            'bdd'         => 'Журнал инструктажей по БДД',
            'report_cart' => 'Журнал снятия отчетов с карт',
            'pechat_pl'   => 'Журнал печати путевых листов',
            //'vid_pl' => 'Реестр выданных путевых листов',
            'pak'         => 'Журнал СДПО',
            'pak_queue'   => 'Очередь на утверждение',
        ];

    public static $fieldsGroupFirst
        = [ // Группа 1 (показываем сразу в HOME)
            'medic' => [
                'date'         => 'Дата и время осмотра',
                'company_name' => 'Место работы',
                'driver_fio'   => 'Водитель',
                'pv_id'        => 'Пункт выпуска',
                //'car_mark_model' => 'Автомобиль',
                'flag_pak'     => 'Флаг СДПО',
                'realy'        => 'Осмотр реальный?',
            ],

            'pak' => [
                'date'           => 'Дата и время осмотра',
                'company_name'   => 'Место работы',
                'driver_fio'     => 'Водитель',
                'pv_id'          => 'Пункт выпуска',
                'car_mark_model' => 'Автомобиль',
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

            'tech' => [
                'date'           => 'Дата, время проведения контроля',
                'car_gos_number' => 'Государственный регистрационный номер транспортного средства',
                'company_name'   => 'Компания',
                'driver_fio'     => 'Водитель',

                // Доп поля
                'pv_id'          => 'Пункт выпуска',
            ],

            'Dop' => [
                'date'           => 'Дата, время проведения осмотра',
                'car_mark_model' => 'Автомобиль',
                'company_name'   => 'Компания',
                'driver_fio'     => 'Водитель',

                // Доп поля
                'pv_id'          => 'Пункт выпуска',
            ],

            'bdd' => [
                'date'          => 'Дата, время',
                'created_at'    => 'Дата внесения в журнал',
                'company_name'  => 'Компания',
                'type_briefing' => 'Вид инструктажа',
                'driver_fio'    => 'Ф.И.О водителя, прошедшего инструктаж',
                'user_name'     => 'Ф.И.О (при наличии) лица, проводившего инструктаж',
                'pv_id'         => 'Пункт выпуска',
                'signature'     => 'ЭЛ подпись водителя',
            ],

            'report_cart' => [
                'date'         => 'Дата снятия отчета',
                'driver_fio'   => 'Ф.И.О водителя',
                'company_name' => 'Компания',
                'user_name'    => 'Ф.И.О (при наличии) лица, проводившего снятие',
                'pv_id'        => 'Пункт выпуска',
            ],

            'pechat_pl' => [
                'date'         => 'Дата распечатки ПЛ',
                'driver_fio'   => 'ФИО водителя',
                'company_name' => 'Компания',
                'count_pl'     => 'Количество распечатанных ПЛ',
                'user_name'    => 'Ф.И.О сотрудника, который готовил ПЛ',
                'pv_id'        => 'Пункт выпуска',
            ],

            /*'vid_pl' => [
                'date' => 'Дата выдачи ПЛ',
                'company_name' => 'Компания',
                'count_pl' => 'Количество выданных ПЛ',
                'user_name' => 'Ф.И.О сотрудника, который выдал ПЛ',
                'pv_id' => 'Пункт выпуска',
                'added_to_dop' => 'Внесено в журнал ТО',
                'added_to_mo' => 'Внесено в журнал МО',
                'period_pl' => 'Комментарий',
                'car_gos_number' => 'Госномер автомобиля'
            ]*/
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

            ],
        ];

    public static $fieldsKeys
        = [ // Группа 2 (скрыты по умолчанию)
            'tech_export_to' => [
                'date'               => 'Дата, время проведения контроля',
                'car_mark_model'     => 'Наименование марки, модели ТС',
                'car_gos_number'     => 'Гос.регистрационный номер ТС',
                'driver_fio'         => 'ФИО водителя',
                'odometer'           => 'Показания одометра  (полные километры пробега при проведении контроля)',
                'point_reys_control' => 'Отметка о прохождении контроля',
                'user_name'          => 'ФИО лица, проводившего контроль',
                'user_eds'           => 'Подпись лица, проводившего контроль (ЭЦП)',
            ],
            'tech_export_pl' => [
                'number_list_road' => 'Номер ПЛ',
                'date'             => 'Дата и время выдачи ПЛ',
                'car_mark_model'   => 'Наименование марки, модели транспортного средства',
                'car_gos_number'   => 'Государственный регистрационный номер транспортного средства',
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
                'date'                   => 'Дата и время осмотра',
                'driver_fio'             => 'ФИО работника',
                'driver_gender'          => 'Пол',
                'driver_year_birthday'   => 'Дата рождения',
                'company_name'           => 'Место работы',
                'complaint'              => 'Жалобы',
                'condition_visible_sliz' => 'Состояние видимых слизистых',
                'condition_koj_pokr'     => 'Состояние кожных покровов',
                't_people'               => 'Температура тела',
                'tonometer'              => "Артериальное давление",
                'pulse'                  => 'Пульс',
                'proba_alko'             => 'Признаки опьянения', // Проба на алкоголь
                'test_narko'             => 'Тест на наркотики',
                'admitted'               => 'Заключение о результатах осмотра',
                'user_name'              => 'ФИО медицинского работника',
                'user_eds'               => 'ЭЦП медицинского работника',

                // Поля не в выгрузку
                'created_at'             => 'Дата создания',
                'driver_group_risk'      => 'Группа риска',
                'company_id'             => 'ID компании',
                'driver_id'              => 'ID водителя',
                'photos'                 => 'Фото',
                'videos'                 => 'Видео',
                'med_view'               => 'Мед показания',
                'pv_id'                  => 'Пункт выпуска',
                //'car_mark_model' => 'Автомобиль',
                //'car_id' => 'ID автомобиля',
                //'number_list_road' => 'Номер путевого листа',
                'type_view'              => 'Тип осмотра',
                'flag_pak'               => 'Флаг СДПО',
                'realy'                  => 'Осмотр реальный?',
                'is_dop'                 => 'Режим ввода ПЛ',
                'period_pl'              => 'Период выдачи ПЛ',
            ],

            'pak' => [
                'date'                   => 'Дата и время осмотра',
                'user_name'              => 'ФИО работника',
                'driver_gender'          => 'Пол',
                'driver_year_birthday'   => 'Дата рождения',
                'company_name'           => 'Место работы',
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
                'driver_fio'             => 'Водитель',
                'company_id'             => 'ID компании',
                'driver_id'              => 'ID водителя',
                'photos'                 => 'Фото',
                'test_narko'             => 'Тест на наркотики',
                'med_view'               => 'Мед показания',
                'pv_id'                  => 'Пункт выпуска',
                'car_mark_model'         => 'Автомобиль',
                'car_id'                 => 'ID автомобиля',
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

            /**
             * Технический осмотр (параметры полей)
             */
            'tech'      => [
                'date'           => 'Дата, время проведения контроля',
                'created_at'     => 'Дата создания',
                'car_mark_model' => 'Наименование марки, модели транспортного средства',
                'car_gos_number' => 'Государственный регистрационный номер транспортного средства',
                'company_name'   => 'Компания',
                'driver_fio'     => 'ФИО Водителя',

                // ID'шники
                'company_id'     => 'ID компании',
                'driver_id'      => 'ID водителя',
                'car_id'         => 'ID автомобиля',

                // Доп поля

                'number_list_road'   => 'Номер ПЛ',
                //'date_number_list_road' => 'Срок действия путевого листа',
                'odometer'           => 'Показания одометра (полные километры пробега) при проведении контроля',
                'point_reys_control' => 'Отметка о прохождении контроля',
                'type_view'          => 'Тип осмотра',
                'user_name'          => 'Фамилия, имя, отчество (при наличии) лица, проводившего контроль',
                'user_eds'           => 'Подпись лица, проводившего контроль',
                'pv_id'              => 'Пункт выпуска',
                'is_dop'             => 'Режим ввода ПЛ',
                'period_pl'          => 'Период выдачи ПЛ',
            ],

            /**
             * Дополнительные осмотр (параметры полей)
             */
            'Dop'       => [
                'date'           => 'Дата и время осмотра',
                'company_name'   => 'Компания',
                'driver_fio'     => 'Водитель',
                'car_mark_model' => 'Автомобиль',
                'car_gos_number' => 'Госномер',

                // ID'шники
                'company_id'     => 'ID компании',
                'driver_id'      => 'ID водителя',
                'car_id'         => 'ID автомобиля',

                // Доп поля

                'number_list_road' => 'Номер путевого листа',
                //'date_number_list_road' => 'Срок действия путевого листа',
                'pv_id'            => 'Пункт выпуска',
                'user_name'        => 'ФИО сотрудника',
                'user_eds'         => 'ЭЦП контролера',
                'created_at'       => 'Дата создания',
            ],

            'Dop_prikaz'  => [
                'number_list_road' => 'Номер ПЛ',
                'date'             => 'Дата и время выдачи ПЛ',
                'car_mark_model'   => 'Наименование марки, модели транспортного средства',
                'car_gos_number'   => 'Государственный регистрационный номер транспортного средства',
                'driver_fio'       => 'ФИО водителя',
                'user_name'        => 'ФИО лица, выдавшего ПЛ',
                'user_eds'         => 'Подпись лица, выдавшего ПЛ (ЭЦП)',
            ],

            /**
             * ЖУРНАЛЫ
             */
            'report_cart' => [
                'date'         => 'Дата снятия отчета',
                'driver_fio'   => 'Ф.И.О водителя',
                'company_name' => 'Компания',
                'user_name'    => 'Ф.И.О (при наличии) лица, проводившего снятие',
                'user_eds'     => 'Подпись лица, проводившего снятие',
                'pv_id'        => 'Пункт выпуска',
                'company_id'   => 'ID компании',
                'driver_id'    => 'ID водителя',
                'signature'    => 'ЭЛ подпись водителя',
            ],

            'pechat_pl' => [
                'date'         => 'Дата распечатки ПЛ',
                'driver_fio'   => 'ФИО водителя',
                'company_name' => 'Компания',
                'count_pl'     => 'Количество распечатанных ПЛ',
                'user_name'    => 'Ф.И.О сотрудника, который готовил ПЛ',
                'user_eds'     => 'ЭЦП сотрудника',
                'pv_id'        => 'Пункт выпуска',
            ],

            /*'vid_pl' => [
                'date' => 'Дата выдачи ПЛ',
                'company_name' => 'Компания',
                'count_pl' => 'Количество выданных ПЛ',
                'user_name' => 'Ф.И.О сотрудника, который выдал ПЛ',
                'user_eds' => 'ЭЦП сотрудника',
                'pv_id' => 'Пункт выпуска',
                'driver_id' => 'ID водителя',
                'driver_fio' => 'Ф.И.О водителя',
                'car_gos_number' => 'Государственный регистрационный номер транспортного средства',
                'car_id' => 'ID автомобиля',
                'added_to_dop' => 'Внесено в журнал ТО',
                'added_to_mo' => 'Внесено в журнал МО',
                'period_pl' => 'Комментарий'
            ],*/

            'bdd' => [
                'date'          => 'Дата, время',
                'created_at'    => 'Дата внесения в журнал',
                'company_name'  => 'Компания',
                'type_briefing' => 'Вид инструктажа',
                'driver_fio'    => 'Ф.И.О водителя, прошедшего инструктаж',
                'user_name'     => 'Ф.И.О (при наличии) лица, проводившего инструктаж',
                'pv_id'         => 'Пункт выпуска',
                'user_eds'      => 'Подпись лица, проводившего инструктаж',
                'company_id'    => 'ID компании',
                'driver_id'     => 'ID водителя',
                'signature'     => 'ЭЛ подпись водителя',
            ],

        ];


    public static function getAll()
    {
        return self::all();
    }
}
