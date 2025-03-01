<?php

namespace App\Actions\Anketa;

use App\Company;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Enums\FlagPakEnum;
use App\Enums\FormTypeEnum;
use App\Events\Forms\DriverDismissed;
use App\MedicFormNormalizedPressure;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Models\Forms\ReportCartForm;
use App\User;
use App\ValueObjects\Pulse;
use App\ValueObjects\Temperature;
use App\ValueObjects\Tonometer;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateSdpoFormHandler extends CreateMedicFormHandler
{
    /**
     * @throws Exception
     */
    public function handle(array $data, Authenticatable $user): array
    {
        $this->init();

        $this->data = $data;

        /** @var User $apiClient */
        $apiClient = $user;
        if ($apiClient->blocked) {
            throw new Exception('Этот терминал заблокирован!');
        }

        $formType = $data['type_anketa'];
        if (!in_array($formType, [FormTypeEnum::PAK_QUEUE, FormTypeEnum::MEDIC])) {
            throw new Exception('Регистрация неподдерживаемого осмотра через СДПО');
        }

        $user = User::find($data['user_id'] ?? 0);
        if (empty($user)) {
            throw new Exception('Пользователь с таким ID не найден!');
        }

        $this->user = $user;
        $this->addUserInfo();

        $this->data['pv_id'] = $apiClient->pv->name;
        $this->data['point_id'] = $apiClient->pv->id;
        $this->data['terminal_id'] = $apiClient->id;

        date_default_timezone_set('UTC');
        $this->time = date('Y-m-d H:i:s', time() + ($user->timezone ?: 3) * 3600);

        foreach ($this->data['anketa'] as $form) {
            $this->createForm($form);
        }

        return [
            'createdId' => $this->createdForms->pluck('id')->toArray(),
            'errors' => array_unique($this->errors),
            'type' => $formType,
            'ankets' => $this->createdForms
        ];
    }

    protected function createForm(array $form)
    {
        $driverId = $this->data['driver_id'] ?? 0;
        $driver = Driver::where('hash_id', $driverId)->first();

        $defaultDatas = [
            'tonometer' => strval(Tonometer::random($driver)),
            't_people' => Temperature::random()->getTemperature(),
            'pulse' => Pulse::random()->getPulse(),
            'date' => date('Y-m-d H:i:s'),
            'test_narko' => 'Отрицательно',
            'proba_alko' => 'Отрицательно',
            'med_view' => 'В норме',
            'admitted' => 'Допущен',
            'realy' => 'да',
            'is_dop' => 0,
            'created_at' => $this->time,
            'flag_pak' => FlagPakEnum::SDPO_A
        ];

        $form = $this->mergeFormData($form, $defaultDatas);

        if (isset($form['driver_id'])) {
            $driverDop = Driver::where('hash_id', $form['driver_id'])->first();

            if ($driverDop) {
                $form['driver_id'] = $driverDop->hash_id;
                $form['driver_fio'] = $driverDop->fio;
            }

            $driver =  $driverDop;
        }

        if (!$driver) {
            $errMsg = 'Водитель не найден';

            $this->errors[] = $errMsg;

            $this->saveSdpoFormWithError($form, $errMsg);

            return;
        }

        /**
         * Проверка водителя по: тесту наркотиков, возрасту
         */
        /** @var Driver $driver */
        $driver->checkGroupRisk(
            $form['tonometer'],
            $form['test_narko'],
            $form['proba_alko']
        );

        if ($driver->dismissed === 'Да') {
            $this->errors[] = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
        }

        if (!$driver->company_id) {
            $errMsg = 'У Водителя не найдена компания';

            $this->errors[] = $errMsg;

            $this->saveSdpoFormWithError($form, $errMsg);

            return;
        }

        $company = Company::find($driver->company_id);

        if (!$company) {
            $errMsg = 'У Водителя не верно указано ID компании';

            $this->errors[] = $errMsg;

            $this->saveSdpoFormWithError($form, $errMsg);

            return;
        }

        if ($company->dismissed === 'Да') {
            $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);

            return;
        }

        if ($driver->year_birthday && $driver->year_birthday !== '0000-00-00') {
            $form['driver_year_birthday'] = $driver->year_birthday;
        }

        $form['driver_gender'] = $driver->gender ?? '';
        $form['driver_fio'] = $driver->fio;
        $form['driver_group_risk'] = $driver->group_risk;

        $form['company_id'] = $company->hash_id;
        $form['company_name'] = $company->name;

        /**
         * ПРОВЕРЯЕМ статус для поля "Заключение"
         */
        if (!$this->admit($form, $driver)) {
            $form['admitted'] = 'Не допущен';
        }

        /**
         * Проверка дат при вводе БДД и Отчета
         */
        if ($form['type_anketa'] === FormTypeEnum::MEDIC) {
            $driver->date_prmo = $form['created_at'];
            $driver->save();
        }

        /**
         * Выставляем ручной режим, если так пришло из ПАК
         */
        if ($form['type_anketa'] === FormTypeEnum::PAK_QUEUE) {
            $form['flag_pak'] = FlagPakEnum::SDPO_R;
        }

        /**
         * Создаем анкету
         */
        $formModel = Form::create($form);

        $formDetailsModel = new MedicForm($form);
        $formDetailsModel->setAttribute('forms_uuid', $formModel->uuid);
        $formDetailsModel->save();

        $this->createdForms->push($formModel);

        if ($this->needStoreNormalizedPressure) {
            MedicFormNormalizedPressure::store(
                $formModel->id,
                Tonometer::fromString($formModel->tonometer)->getNormalized()
            );
        }

        /**
         * ОТПРАВКА SMS
         */
        $needNotify = $form['admitted'] === 'Не допущен' && $form['flag_pak'] !== FlagPakEnum::SDPO_R;
        if ($needNotify) {
            event(new DriverDismissed($formModel));
        }
    }

    protected function saveSdpoFormWithError(array $form, string $comment = '')
    {
        if (!isset($form['is_pak'])) {
            return;
        }

        $form['type_anketa'] = 'pak';
        $form['comments'] = $comment;

        $formModel = Form::create($form);

        $formDetailsModel = new ReportCartForm($form);
        $formDetailsModel->setAttribute('forms_uuid', $formModel->uuid);
        $formDetailsModel->save();
    }
}
