<?php

namespace App;

use App\Models\ContractAnketaSnapshot;
use App\Services\Helpers\ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Anketa
 *
 * @property string tonometer
 * @property ArrayObject tonometer_data
 * @property array tonometer_data_int
 * @property float t_people voodoo people
 * @property int $id
 * @property string $type_anketa
 * @property int $user_id
 * @property string|null $user_eds
 * @property string|null $user_name
 * @property string|null $pv_id
 * @property int|null $driver_id
 * @property string|null $driver_group_risk
 * @property string|null $driver_fio
 * @property string|null $driver_gender
 * @property string|null $driver_year_birthday
 * @property string|null $car_id
 * @property string|null $car_mark_model
 * @property string|null $car_gos_number
 * @property string $complaint
 * @property string $condition_visible_sliz
 * @property string $condition_koj_pokr
 * @property string|null $date
 * @property string|null $number_list_road
 * @property string|null $date_number_list_road
 * @property string $type_view
 * @property int|null $company_id
 * @property string|null $company_name
 * @property string $proba_alko
 * @property string $test_narko
 * @property string $med_view
 * @property string $admitted
 * @property int|null $pulse
 * @property int $alcometer_mode
 * @property int|null $alcometer_result
 * @property string|null $type_trip
 * @property string|null $questions
 * @property string|null $odometer
 * @property string $point_reys_control
 * @property int $in_cart
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $date_pechat_pl
 * @property int $count_pl
 * @property string|null $type_briefing
 * @property string|null $photos
 * @property int $is_pak
 * @property string|null $protokol_path
 * @property string|null $comments
 * @property string $added_to_dop
 * @property string|null $period_pl
 * @property string|null $flag_pak
 * @property string|null $realy
 * @property string|null $added_to_mo
 * @property string|null $videos
 * @property int|null $is_dop
 * @property string|null $result_dop
 * @property string|null $connected_hash
 * @property string|null $signature
 * @property mixed|null $deleted_at
 * @property string|null $deleted_id
 * @property int|null $contract_id
 * @property int|null $contract_snapshot_id
 * @property int|null $terminal_id
 * @property int|null $point_id
 * @property string|null $briefing_name
 * @property-read \App\Car|null $car
 * @property-read \App\Company|null $company
 * @property-read ContractAnketaSnapshot|null $contract_snapshot
 * @property-read \App\User|null $deleted_user
 * @property-read \App\Driver|null $driver
 * @property-read \App\Req $our_company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $services_snapshot
 * @property-read int|null $services_snapshot_count
 * @property-read \App\User $user
 * @method static Builder|Anketa newModelQuery()
 * @method static Builder|Anketa newQuery()
 * @method static Builder|Anketa query()
 * @method static Builder|Anketa whereAddedToDop($value)
 * @method static Builder|Anketa whereAddedToMo($value)
 * @method static Builder|Anketa whereAdmitted($value)
 * @method static Builder|Anketa whereAlcometerMode($value)
 * @method static Builder|Anketa whereAlcometerResult($value)
 * @method static Builder|Anketa whereBriefingName($value)
 * @method static Builder|Anketa whereCarGosNumber($value)
 * @method static Builder|Anketa whereCarId($value)
 * @method static Builder|Anketa whereCarMarkModel($value)
 * @method static Builder|Anketa whereComments($value)
 * @method static Builder|Anketa whereCompanyId($value)
 * @method static Builder|Anketa whereCompanyName($value)
 * @method static Builder|Anketa whereComplaint($value)
 * @method static Builder|Anketa whereConditionKojPokr($value)
 * @method static Builder|Anketa whereConditionVisibleSliz($value)
 * @method static Builder|Anketa whereConnectedHash($value)
 * @method static Builder|Anketa whereContractId($value)
 * @method static Builder|Anketa whereContractSnapshotId($value)
 * @method static Builder|Anketa whereCountPl($value)
 * @method static Builder|Anketa whereCreatedAt($value)
 * @method static Builder|Anketa whereDate($value)
 * @method static Builder|Anketa whereDateNumberListRoad($value)
 * @method static Builder|Anketa whereDatePechatPl($value)
 * @method static Builder|Anketa whereDeletedAt($value)
 * @method static Builder|Anketa whereDeletedId($value)
 * @method static Builder|Anketa whereDriverFio($value)
 * @method static Builder|Anketa whereDriverGender($value)
 * @method static Builder|Anketa whereDriverGroupRisk($value)
 * @method static Builder|Anketa whereDriverId($value)
 * @method static Builder|Anketa whereDriverYearBirthday($value)
 * @method static Builder|Anketa whereFlagPak($value)
 * @method static Builder|Anketa whereId($value)
 * @method static Builder|Anketa whereInCart($value)
 * @method static Builder|Anketa whereIsDop($value)
 * @method static Builder|Anketa whereIsPak($value)
 * @method static Builder|Anketa whereMedView($value)
 * @method static Builder|Anketa whereNumberListRoad($value)
 * @method static Builder|Anketa whereOdometer($value)
 * @method static Builder|Anketa wherePeriodPl($value)
 * @method static Builder|Anketa wherePhotos($value)
 * @method static Builder|Anketa wherePointId($value)
 * @method static Builder|Anketa wherePointReysControl($value)
 * @method static Builder|Anketa whereProbaAlko($value)
 * @method static Builder|Anketa whereProtokolPath($value)
 * @method static Builder|Anketa wherePulse($value)
 * @method static Builder|Anketa wherePvId($value)
 * @method static Builder|Anketa whereQuestions($value)
 * @method static Builder|Anketa whereRealy($value)
 * @method static Builder|Anketa whereResultDop($value)
 * @method static Builder|Anketa whereSignature($value)
 * @method static Builder|Anketa whereTPeople($value)
 * @method static Builder|Anketa whereTerminalId($value)
 * @method static Builder|Anketa whereTestNarko($value)
 * @method static Builder|Anketa whereTonometer($value)
 * @method static Builder|Anketa whereTypeAnketa($value)
 * @method static Builder|Anketa whereTypeBriefing($value)
 * @method static Builder|Anketa whereTypeTrip($value)
 * @method static Builder|Anketa whereTypeView($value)
 * @method static Builder|Anketa whereUpdatedAt($value)
 * @method static Builder|Anketa whereUserEds($value)
 * @method static Builder|Anketa whereUserId($value)
 * @method static Builder|Anketa whereUserName($value)
 * @method static Builder|Anketa whereVideos($value)
 * @mixin \Eloquent
 */
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
//        return $this->belongsTo(ContractAnketaSnapshot::class, 'contract_snapshot_id', 'id')
//                    ->withDefault();
    }


//    public function contract()
//    {
////        return $this->belongsTo(Contract::class, 'contract_id', 'id')
////                    ->withDefault();
//        return $this->hasOne(
//            Contract::class,
//            'contract_id',
//            'id'
//        )
//                    ->withDefault();
//    }

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
                'driver_id'    => 'Водитель',
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
                'driver_id'      => 'Водитель',
                'car_id'         => 'Гос. регистрационный номер ТС',
                'date'           => 'Дата осмотра',
                'period_pl'      => 'Период выдачи ПЛ',
                'realy'          => 'Осмотр реальный?',
                'car_type_auto'  => 'Категория ТС',
                'type_view'      => 'Тип осмотра',
                'pv_id'          => 'Пункт выпуска',
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
                'driver_id'              => 'Водитель',

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
                'driver_fio'      => 'Водитель',
                'car_id'         => 'Гос. регистрационный номер ТС',
                'date'           => 'Дата, время проведения контроля',
                'period_pl'      => 'Период выдачи ПЛ',
                'created_at'     => 'Дата создания',
                'realy'          => 'Осмотр реальный?',
                'car_type_auto'  => 'Категория ТС',
                'car_mark_model' => 'Марка автомобиля',
                'type_view'          => 'Тип осмотра',

                // Доп поля

                'number_list_road'   => 'Номер ПЛ',
                //'date_number_list_road' => 'Срок действия путевого листа',
                'odometer'           => 'Показания одометра',
                'point_reys_control' => 'Отметка о прохождении контроля',
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
                'pv_id' => 'Пункт выпуска',
                'tonometer' => "Артериальное давление",
                't_people' => 'Температура тела',
                'proba_alko' => 'Признаки опьянения', // Проба на алкоголь
                'complaint' => 'Жалобы',
                'admitted' => 'Заключение о результатах осмотра',
                'photos' => 'Фото',
                'videos' => 'Видео',
            ],
        ];


    public static function getAll()
    {
        return self::all();
    }

    public const BLOOD_PRESSURE_THRESHOLDS = [
        'systolic' => [50, 220],
        'diastolic' => [40, 160],
    ];

    public const HUMAN_BODY_TEMPERATURE_THRESHOLDS = [35.5, 37.5];

    /**
     * Mutator
     * @property array tonometer_data_int
     * @property string tonometer
     */
    public function getTonometerDataIntAttribute(): ArrayObject
    {
        return new ArrayObject(explode('/', $this->tonometer));
    }

    /**
     * Mutator
     * @return array
     * @throws \Exception
     * @property string tonometer
     */
    public function getTonometerDataAttribute(): array
    {
        return ['systolic' => $this->tonometer_data_int[0], 'diastolic' => $this->tonometer_data_int[1]];

    }

    public function checkTemperatureFine(array $thresholds = self::HUMAN_BODY_TEMPERATURE_THRESHOLDS): bool
    {
        return in_array_thresholds($this->t_people, $thresholds);
    }

    public function checkBloodPressureFine(array $thresholds = self::BLOOD_PRESSURE_THRESHOLDS): bool
    {
        return (
            in_array_thresholds($this->tonometer_data['systolic'], $thresholds['systolic']) and
            in_array_thresholds($this->tonometer_data['diastolic'], $thresholds['diastolic'])
        );
    }
}
