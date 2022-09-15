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
            'name'        => 'field_prompt_create',
            'guard_name' => 'Подсказки полей - Добавление',
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
            'field_prompt_create',
            'field_prompt_delete',
            'field_prompt_trash',
        ]);
    }
}
