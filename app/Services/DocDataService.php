<?php

namespace App\Services;

use App\Company;
use App\Driver;
use App\Models\Forms\Form;
use App\Point;
use App\User;

class DocDataService
{
    public function get(Form $form): array
    {
        $data = [
            'anketa_id' => $form->id,
            'driver_fio' => '',
            'driver_year_birthday' => '',
            'driver_yb' => '',
            'driver_pv' => '',
            'company_name' => '',
            'user_name' => '',
            'user_fio' => '',
            'user_company' => '',
            'date' => '',
            'town' => '',
            'drugs' => false,
            'alko' => false,
            'status' => 'Нет',
            'alcometer' => config('docs.fields.alcometer')[0],
            'alcometer_serial_number' => '',
            'point' => ''
        ];

        $details = $form->details;

        $data = array_merge($data, $form->toArray(), $details->toArray());

        $driver = Driver::withTrashed()->where('hash_id', $form->driver_id)->first();
        $data['driver'] = $driver;
        $data['driver_year_birthday'] = $driver->year_birtday;
        $data['driver_fio'] = $driver->fio;

        if ($details->test_narko === 'Положительно') {
            $data['drugs'] = true;
        }

        if ($details->proba_alko === 'Положительно') {
            $data['alko'] = true;
        }

        $data['alcometer_result'] = $data['alcometer_result'] ?? 0;

        if ($details->med_view === 'Отстранение') {
            $data['status'] = 'Есть жалобы';
        }

        $data['user_post'] = $this->getUserRole($form->user_id);

        if ($data['alko']) {
            $recommendations = 'Пройдите медицинское освидетельствование на состояние алкогольного опьянения';
        } else {
            $recommendations = '*Срочно обратитесь к врачу по месту жительства';
        }
        $data['recommendations'] = $recommendations;

        if ($form->company_id) {
            $company = Company::withTrashed()->where('hash_id', $form->company_id)->first();

            if ($company) {
                $point = Point::find($company->pv_id);

                $data['company_name'] = $company->name;

                if ($point) {
                    $data['driver_pv'] = $point->name;
                }
            }
        }

        if ($form->point_id) {
            $point = Point::withTrashed()->with('town')->find($form->point_id);

            if ($point) {
                $data['town'] = $point->town->name;
                $data['point'] = $point->name;
            }
        }

        if ($form->user_id) {
            $user = User::withTrashed()->find($form->user_id);

            if ($user) {
                $data['user_name'] = $user->name;
            }
        }

        if ($data['complaint'] === 'Нет') {
            $data['complaint'] = 'Отсутствуют';
        }

        $data = $this->getUserEds($data);
        $data = $this->getClosing($data);

        return $this->getComment($data);
    }

    protected function getUserRole($userId): string
    {
        if (empty($userId)) {
            return '';
        }

        $user = User::find($userId);

        if (empty($user)) {
            return '';
        }

        switch ($user->role) {
            case 12:
                $role = 'Клиент';
                break;
            case 4:
                $role = 'Оператор СДПО';
                break;
            case 1:
                $role = 'Контролёр ТС';
                break;
            case 2:
                $role = 'Медицинский сотрудник';
                break;
            case 3:
                $role = 'Водитель';
                break;
            case 11:
                $role = 'Менеджер';
                break;
            case 13:
                $role = 'Инженер БДД';
                break;
            case 777:
                $role = 'Администратор';
                break;
            case 778:
                $role = 'Терминал';
                break;
            default:
                $role = '';
        }

        return $role;
    }

    protected function getClosing(array $data): array
    {
        if ($data['type_view'] === 'Предрейсовый/Предсменный') {
            $closingRows = [
                "прошел предсменный (предрейсовый) медицинский осмотр, к исполнению трудовых обязанностей НЕ допущен",
            ];

            $descriptionRow = "наличие признаков воздействия вредных и (или) опасных производственных факторов, состояний и заболеваний, препятствующих выполнению трудовых обязанностей";

            if ($data['alko']) {
                $descriptionRow .= ", в том числе алкогольного, наркотического или иного токсического опьянения и остаточных явлений такого опьянения";
            }

            $closingRows[] = $descriptionRow;
        } else {
            $closingRows = [];

            $descriptionRow = "прошел послесменный, послерейсовый медицинский осмотр, выявлены признаки воздействия вредных и (или) опасных производственных факторов рабочей среды и трудового процесса на состояние здоровья работника, острого профессионального заболевания или отравления";

            if ($data['alko']) {
                $descriptionRow .= ", признаков алкогольного, наркотического или иного токсического опьянения";
            }

            $closingRows[] = $descriptionRow;
        }

        $data['closing'] = implode("\n", $closingRows);

        return $data;
    }

    protected function getComment(array $data): array
    {
        $commentRows = [];

        if (!$data['alko']) {
            $commentRows = [
                "*Предлагаем Вам несколько простых и доступных советов для профилактики обострений и осложнений гипертонической болезни.",
                "Соблюдение режима труда и отдыха;",
                "Регулярный контроль уровня АД, ЧСС;",
                "Не пропускайте прием медикаментов по назначению врача;",
                "Отказ от курения. алкоголя, энергетиков, крепкого чая, кофе;",
                "Ограничение потребление соли,  суточная норма не более 3,5 г (1-1,5 чайные ложки поваренной соли) во всех продуктах и напитках суточного рациона;",
                "Сбалансировать калорийность питания, исключить  консервированные, соленые, копченые продукты, фастфуд;"
            ];
        }

        $data['comment'] = implode("\n", $commentRows);
        $data['comment_rows'] = count($commentRows);

        return $data;
    }

    protected function getUserEds(array$data): array
    {
        $userEdsRows = [
            $data['user_eds']
        ];

        $userEdsPeriodRow = '';
        if ($data['user_validity_eds_start']) {
            $userEdsPeriodRow .= "c " . $data['user_validity_eds_start'];
        }

        if ($data['user_validity_eds_end']) {
            $userEdsPeriodRow .= " по " . $data['user_validity_eds_end'];
        }

        $userEdsPeriodRow = trim($userEdsPeriodRow);
        if (strlen($userEdsPeriodRow)) {
            $userEdsRows[] = $userEdsPeriodRow;
        }

        $data['user_eds'] = implode("\n", $userEdsRows);

        return $data;
    }
}
