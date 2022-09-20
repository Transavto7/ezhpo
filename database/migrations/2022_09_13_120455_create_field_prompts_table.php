<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class CreateFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_prompts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('field');
            $table->string('name');
            $table->text('content')->nullable();
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        $this->addAllFields();

        // Add new permissions
        Permission::updateOrCreate([
            'name'        => 'field_prompt_read',
            'guard_name' => 'Подсказки полей - Просмотр',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_edit',
            'guard_name' => 'Подсказки полей - Редактирование',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_delete',
            'guard_name' => 'Подсказки полей - Удаление',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_trash',
            'guard_name' => 'Подсказки полей - Карзина',
        ]);
    }

    /*
     * Add all fields in table
     */
    public function addAllFields() {
        //medic
        $this->addField('medic', 'id', 'ID записи');
        $this->addField('medic', 'company_name', 'Место работы');
        $this->addField('medic', 'company_id', 'ID компании');
        $this->addField('medic', 'date', 'Дата и время осмотра');
        $this->addField('medic', 'period_pl', 'Период выдачи ПЛ');
        $this->addField('medic', 'driver_fio', 'ФИО работника');
        $this->addField('medic', 'realy', 'Осмотр реальный?');
        $this->addField('medic', 'driver_group_risk', 'Группа риска');
        $this->addField('medic', 'type_view', 'Тип осмотра');
        $this->addField('medic', 'proba_alko', 'Признаки опьянения');
        $this->addField('medic', 'test_narko', 'Тест на наркотики');
        $this->addField('medic', 'driver_gender', 'Пол');
        $this->addField('medic', 'driver_year_birthday', 'Дата рождения');
        $this->addField('medic', 'complaint', 'Жалобы');
        $this->addField('medic', 'condition_visible_sliz', 'Состояние видимых слизистых');
        $this->addField('medic', 'condition_koj_pokr', 'Состояние кожных покровов');
        $this->addField('medic', 't_people', 'Температура тела');
        $this->addField('medic', 'tonometer', 'Артериальное давление');
        $this->addField('medic', 'pulse', 'Пульс');
        $this->addField('medic', 'test_narko', 'Тест на наркотики');
        $this->addField('medic', 'admitted', 'Заключение о результатах осмотра');
        $this->addField('medic', 'user_name', 'ФИО ответственного');
        $this->addField('medic', 'user_eds', 'ЭЦП медицинского работника');
        $this->addField('medic', 'created_at', 'Дата создания');
        $this->addField('medic', 'driver_id', 'ID водителя');
        $this->addField('medic', 'photos', 'Фото');
        $this->addField('medic', 'videos', 'Видео');
        $this->addField('medic', 'med_view', 'Мед показания');
        $this->addField('medic', 'pv_id', 'Пункт выпуска');
        $this->addField('medic', 'flag_pak', 'Флаг СДПО');
        $this->addField('medic', 'is_dop', 'Режим ввода ПЛ');

        // tech
        $this->addField('tech', 'id', 'ID записи');
        $this->addField('tech', 'company_id', 'ID Компании');
        $this->addField('tech', 'company_name', 'Компания');
        $this->addField('tech', 'date', 'Дата, время проведения контроля');
        $this->addField('tech', 'period_pl', 'Период выдачи ПЛ');
        $this->addField('tech', 'created_at', 'Дата создания');
        $this->addField('tech', 'car_gos_number', 'Гос.регистрационный номер ТС');
        $this->addField('tech', 'realy', 'Осмотр реальный?');
        $this->addField('tech', 'car_type_auto', 'Категория ТС');
        $this->addField('tech', 'car_mark_model', 'Марка автомобиля');
        $this->addField('tech', 'type_view', 'Тип осмотра');
        $this->addField('tech', 'driver_fio', 'ФИО Водителя');
        $this->addField('tech', 'driver_id', 'ID водителя');
        $this->addField('tech', 'car_id', 'ID автомобиля');
        $this->addField('tech', 'number_list_road', 'Номер ПЛ');
        $this->addField('tech', 'odometer', 'показания одометра');
        $this->addField('tech', 'point_reys_control', 'Отметка о прохождении контроля');
        $this->addField('tech', 'user_name', 'ФИО ответственного');
        $this->addField('tech', 'user_eds', 'Подпись лица, проводившего контроль');
        $this->addField('tech', 'pv_id', 'Пункт выпуска');
        $this->addField('tech', 'is_dop', 'Режим ввода ПЛ');

        // pak
        $this->addField('pak', 'id', 'ID записи');
        $this->addField('pak', 'date', 'Дата и время осмотра');
        $this->addField('pak', 'user_name', 'ФИО работника');
        $this->addField('pak', 'driver_gender', 'Пол');
        $this->addField('pak', 'driver_year_birthday', 'Дата рождения');
        $this->addField('pak', 'complaint', 'Жалобы');
        $this->addField('pak', 'condition_visible_sliz', 'Состояние видимых слизистых');
        $this->addField('pak', 'condition_koj_pokr', 'Состояние кожных покровов');
        $this->addField('pak', 't_people', 'Температура тела');
        $this->addField('pak', 'tonometer', 'Артериальное давление');
        $this->addField('pak', 'pulse', 'Пульс');
        $this->addField('pak', 'proba_alko', 'Признаки опьянения');
        $this->addField('pak', 'admitted', 'Заключение о результатах осмотра');
        $this->addField('pak', 'user_eds', 'ЭЦП медицинского работника');
        $this->addField('pak', 'created_at', 'Дата создания');
        $this->addField('pak', 'driver_group_risk', 'Группа риска');
        $this->addField('pak', 'driver_fio', 'Водитель');
        $this->addField('pak', 'company_id', 'ID компании');
        $this->addField('pak', 'driver_id', 'ID водителя');
        $this->addField('pak', 'photos', 'Фото');
        $this->addField('pak', 'med_view', 'Мед показания');
        $this->addField('pak', 'pv_id', 'Пункт выпуска');
        $this->addField('pak', 'car_mark_model', 'Автомобиль');
        $this->addField('pak', 'car_id', 'ID автомобиля');
        $this->addField('pak', 'number_list_road', 'Номер путевого листа');
        $this->addField('pak', 'type_view', 'Тип осмотра');
        $this->addField('pak', 'comments', 'Комментарий');
        $this->addField('pak', 'flag_pak', 'Флаг СДПО');

        // pak_queue
        $this->addField('pak_queue', 'id', 'ID записи');
        $this->addField('pak_queue', 'created_at', 'Дата создания');
        $this->addField('pak_queue', 'driver_fio', 'Водитель');
        $this->addField('pak_queue', 'pv_id', 'Пункт выпуска');
        $this->addField('pak_queue', 'tonometer', 'Артериальное давление');
        $this->addField('pak_queue', 't_people', 'Температура тела');
        $this->addField('pak_queue', 'proba_alko', 'Признаки опьянения');
        $this->addField('pak_queue', 'complaint', 'Жалобы');
        $this->addField('pak_queue', 'admitted', 'Заключение о результатах осмотра');
        $this->addField('pak_queue', 'photos', 'Фото');
        $this->addField('pak_queue', 'videos', 'Видео');

        // Dop
        $this->addField('Dop', 'id', 'ID записи');
        $this->addField('Dop', 'date','Дата и время выдачи пл');
        $this->addField('Dop', 'company_name', 'Компания');
        $this->addField('Dop', 'driver_fio', 'ФИО водителя');
        $this->addField('Dop', 'car_mark_model', 'Марка автомобиля');
        $this->addField('Dop', 'car_gos_number', 'Государственный регистрационный номер транспортного средства');
        $this->addField('Dop', 'company_id', 'ID компании');
        $this->addField('Dop', 'driver_id', 'ID водителя');
        $this->addField('Dop', 'car_id', 'ID автомобиля');
        $this->addField('Dop', 'number_list_road', 'Номер путевого листа');
        $this->addField('Dop', 'pv_id', 'Пункт выпуска');
        $this->addField('Dop', 'user_name', 'ФИО ответственного');
        $this->addField('Dop', 'user_eds', 'ЭЦП контролера');
        $this->addField('Dop', 'created_at', 'Дата/Время создания записи');

        // report_cart
        $this->addField('report_cart', 'id', 'ID записи');
        $this->addField('report_cart', 'company_id', 'ID Компании');
        $this->addField('report_cart', 'company_name', 'Компания');
        $this->addField('report_cart', 'date', 'Дата снятия отчета');
        $this->addField('report_cart', 'driver_fio', 'Ф.И.О водителя');
        $this->addField('report_cart', 'user_name', 'Ф.И.О (при наличии) лица, проводившего снятие');
        $this->addField('report_cart', 'user_eds', 'Подпись лица, проводившего снятие');
        $this->addField('report_cart', 'pv_id', 'Пункт выпуска');
        $this->addField('report_cart', 'driver_id', 'ID водителя');
        $this->addField('report_cart', 'signature', 'ЭЛ подпись водителя');
        $this->addField('report_cart', 'created_at', 'Дата/Время создания записи');

        // pechat_pl
        $this->addField('pechat_pl', 'id', 'ID записи');
        $this->addField('pechat_pl', 'company_name', 'Компания');
        $this->addField('pechat_pl', 'company_id', 'ID Компания');
        $this->addField('pechat_pl', 'date', 'Дата распечатки ПЛ');
        $this->addField('pechat_pl', 'driver_fio', 'ФИО водителя');
        $this->addField('pechat_pl', 'count_pl', 'Количество распечатанных ПЛ');
        $this->addField('pechat_pl', 'user_name', 'Ф.И.О сотрудника, который готовил ПЛ');
        $this->addField('pechat_pl', 'user_eds', 'ЭЦП сотрудника');
        $this->addField('pechat_pl', 'pv_id', 'Пункт выпуска');

        // bdd
        $this->addField('bdd', 'company_name', 'Компания');
        $this->addField('bdd', 'company_id', 'ID Компании');
        $this->addField('bdd', 'date', 'Дата, время');
        $this->addField('bdd', 'created_at', 'Дата внесения в журнал');
        $this->addField('bdd', 'type_briefing', 'Вид инструктажа');
        $this->addField('bdd', 'driver_fio', 'Ф.И.О водителя, прошедшего инструктаж');
        $this->addField('bdd', 'user_name', 'Ф.И.О (при наличии) лица, проводившего инструктаж');
        $this->addField('bdd', 'pv_id', 'Пункт выпуска');
        $this->addField('bdd', 'user_eds', 'Подпись лица, проводившего инструктаж');
        $this->addField('bdd', 'driver_id', 'ID водителя');
        $this->addField('bdd', 'signature', 'ЭЛ подпись водителя');

        // Drivers
        $this->addField('driver', 'hash_id', 'ID');
        $this->addField('driver', 'photo', 'Фото');
        $this->addField('driver', 'fio', 'ФИО');
        $this->addField('driver', 'year_birthday', 'Дата рождения');
        $this->addField('driver', 'phone', 'Телефон');
        $this->addField('driver', 'gender', 'Пол');
        $this->addField('driver', 'group_risk', 'Группа риска');
        $this->addField('driver', 'company_id', 'Компания');
        $this->addField('driver', 'products_id', 'Услуги');
        $this->addField('driver', 'note', 'Примечание');
        $this->addField('driver', 'procedure_pv', 'Порядок выпуска');
        $this->addField('driver', 'date_bdd', 'Дата БДД');
        $this->addField('driver', 'date_prmo', 'Дата ПРМО');
        $this->addField('driver', 'date_report_driver', 'Дата снятия отчета с карты водителя');
        $this->addField('driver', 'time_card_driver', 'Срок действия карты водителя');
        $this->addField('driver', 'town_id', 'Город');
        $this->addField('driver', 'dismissed', 'Уволен');
        $this->addField('driver', 'date_of_employment', 'Дата устройства на работу');

        // Cars
        $this->addField('car', 'hash_id', 'ID');
        $this->addField('car', 'gos_number', 'Гос.номер');
        $this->addField('car', 'mark_model', 'Марка и модель');
        $this->addField('car', 'type_auto', 'Тип автомобиля');
        $this->addField('car', 'products_id', 'Услуги');
        $this->addField('car', 'trailer', 'Прицеп');
        $this->addField('car', 'company_id', 'Компания');
        $this->addField('car', 'note', 'Примечание');
        $this->addField('car', 'procedure_pv', 'Порядок выпуска');
        $this->addField('car', 'date_prto', 'Дата ПРТО');
        $this->addField('car', 'date_techview', 'Дата техосмотра');
        $this->addField('car', 'time_skzi', 'Срок действия СКЗИ');
        $this->addField('car', 'date_osago', 'Дата ОСАГО');
        $this->addField('car', 'town_id', 'Город');
        $this->addField('car', 'dismissed', 'Уволен');

        // Companies
        $this->addField('company', 'hash_id', 'ID');
        $this->addField('company', 'name', 'Название компании клиента');
        $this->addField('company', 'crm', 'Реестры');
        $this->addField('company', 'journals', 'Справочники');
        $this->addField('company', 'note', 'Примечание');
        $this->addField('company', 'user_id', 'Ответственный');
        $this->addField('company', 'req_id', 'Реквизиты нашей компании');
        $this->addField('company', 'pv_id', 'ПВ');
        $this->addField('company', 'town_id', 'Город');
        $this->addField('company', 'products_id', 'Услуги');
        $this->addField('company', 'where_call', 'Кому отправлять СМС при отстранении');
        $this->addField('company', 'where_call_name', 'Кому звонить при отстранении (имя, должность)');
        $this->addField('company', 'inn', 'ИНН');
        $this->addField('company', 'procedure_pv', 'Порядок выпуска');
        $this->addField('company', 'dismissed', 'Черный список');

        // products
        $this->addField('product', 'hash_id', 'ID');
        $this->addField('product', 'name', 'Название');
        $this->addField('product', 'type_product', 'Тип');
        $this->addField('product', 'unit', 'Ед.изм.');
        $this->addField('product', 'price_unit', 'Стоимость за единицу');
        $this->addField('product', 'type_anketa', 'Реестр');
        $this->addField('product', 'type_view', 'Тип осмотра');
        $this->addField('product', 'essence', 'Сущности');

        // discounts
        $this->addField('discount', 'hash_id', 'ID');
        $this->addField('discount', 'products_id', 'Услуга');
        $this->addField('discount', 'trigger', 'Триггер (больше/меньше)');
        $this->addField('discount', 'porog', 'Пороговое значение');
        $this->addField('discount', 'discount', 'Скидка (%)');

        // instrs
        $this->addField('instr', 'hash_id', 'ID');
        $this->addField('instr', 'photos', 'Фото');
        $this->addField('instr', 'name', 'Название');
        $this->addField('instr', 'descr', 'Описание');
        $this->addField('instr', 'type_briefing', 'Вид инструктажа');
        $this->addField('instr', 'youtube', 'Ссылка на YouTube');
        $this->addField('instr', 'active', 'Активен');
        $this->addField('instr', 'sort', 'Сортировка');

        // points
        $this->addField('point', 'hash_id', 'ID');
        $this->addField('point', 'name', 'Пункт выпуска');
        $this->addField('point', 'pv_id', 'Город');
        $this->addField('point', 'company_id', 'Компания');

        // town
        $this->addField('town', 'hash_id', 'ID');
        $this->addField('town', 'name', 'Город');

        // users
        $this->addField('users', 'hash_id', 'ID');
        $this->addField('users', 'photo', 'Фото');
        $this->addField('users', 'name', 'ФИО');
        $this->addField('users', 'login', 'Логин');
        $this->addField('users', 'email', 'E-mail');
        $this->addField('users', 'pv', 'ПВ');
        $this->addField('users', 'timezone', 'GMT');
        $this->addField('users', 'blocked', 'Заблокирован');
        $this->addField('users', 'roles', 'Роль');

        // roles
        $this->addField('roles', 'id', 'ID');
        $this->addField('roles', 'guard_name', 'Название');

        // d_dates
        $this->addField('ddates', 'hash_id', 'ID');
        $this->addField('ddates', 'field', 'Поле даты проверки');
        $this->addField('ddates', 'days', 'Кол-во дней');
        $this->addField('ddates', 'action', 'Действие');

        // field history
        $this->addField('fieldhistory', 'hash_id', 'ID');
        $this->addField('fieldhistory', 'user_id', 'Пользователь');
        $this->addField('fieldhistory', 'value', 'Значение');
        $this->addField('fieldhistory', 'field', 'Поле');
        $this->addField('fieldhistory', 'created_at', 'Дата');

        $this->addField('pak_sdpo', 'hash_id', 'ID');
        $this->addField('pak_sdpo', 'api_token', 'Токен');
        $this->addField('pak_sdpo', 'login', 'Логин');
        $this->addField('pak_sdpo', 'email', 'E-mail');
        $this->addField('pak_sdpo', 'pv_id', 'ПВ');
        $this->addField('pak_sdpo', 'company_id', 'Компания');
        $this->addField('pak_sdpo', 'timezone', 'GMT');
        $this->addField('pak_sdpo', 'blocked', 'Заблокирован');
        $this->addField('pak_sdpo', 'roles', 'Роль');

        $this->addField('req', 'hash_id', 'id');
        $this->addField('req', 'name', 'Название');
        $this->addField('req', 'inn', 'ИНН');
        $this->addField('req', 'bik', 'БИК');
        $this->addField('req', 'kc', 'К/С');
        $this->addField('req', 'rc', 'Р/С');
        $this->addField('req', 'banks', 'Банки');
        $this->addField('req', 'director', 'Должность руководителя');
        $this->addField('req', 'director_fio', 'ФИО Руководителя');
        $this->addField('req', 'seal', 'Печать');
    }

    public function addField(string $type, string $field, string $name) {
        FieldPrompt::create([
            'type' => $type,
            'field' => $field,
            'name' => $name
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_prompts');
        Permission::whereIn('name', [
            'field_prompt_read',
            'field_prompt_edit',
            'field_prompt_delete',
            'field_prompt_trash',
        ]);
    }
}
