<?php

namespace App\Services\QRCode;

use App\Car;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Enums\QRCodeLinkParameter;
use App\User;
use Exception;
use Illuminate\Support\Carbon;

class HashIdChecker
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $entityId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param User $user
     * @param string $entityId
     * @param string $type
     */
    public function __construct(User $user, string $entityId, string $type)
    {
        $this->user = $user;
        $this->entityId = $entityId;
        $this->type = $type;
    }

    /**
     * @throws Exception
     */
    public function checkAll()
    {
        $this->checkUser();

        if ($this->type === QRCodeLinkParameter::DRIVER_ID) {
            $this->checkDriver();
        } elseif ($this->type === QRCodeLinkParameter::CAR_ID) {
            $this->checkCar();
        } else {
            throw new \DomainException('Тип сущности не указан или указан неверно. Тип: '.$this->type, 400);
        }
    }

    /**
     * @throws Exception
     */
    private function checkUser()
    {
        if ($this->user->blocked) {
            throw new Exception('Этот терминал заблокирован!', 400);
        }
    }

    /**
     * @throws Exception
     */
    private function checkDriver()
    {
        $driver = Driver::where('hash_id', $this->entityId)->first();
        if (!$driver) {
            throw new Exception('Указанный водитель не найден', 400);
        }

        date_default_timezone_set('UTC');
        $time = time();
        $apiClient = $this->user;
        $timezone = $apiClient->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d H:i:s', $time);

        if ($driver->end_of_ban && (Carbon::parse($time) < Carbon::parse($driver->end_of_ban))) {
            $message = sprintf("Указанный водитель отстранен до %s", Carbon::parse($driver->end_of_ban));
            throw new Exception($message, 400);
        }

        if ($driver->dismissed === 'Да') {
            throw new Exception('Водитель с указанным ID уволен', 303);
        }

        $company = $driver->company;
        if ($company->dismissed === 'Да') {
            $message = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
            throw new Exception($message, 401);
        }

        if ($driver->only_offline_medic_inspections) {
            $message = 'Водителю ограничен дистанционный выпуск, обратитесь к медицинскому сотруднику на Пункте Выпуска';
            throw new Exception($message, 400);
        }
    }

    /**
     * @throws Exception
     */
    private function checkCar()
    {
        $car = Car::where('hash_id', $this->entityId)->first();
        if (!$car) {
            throw new Exception('Указанный автомобиль не найден', 400);
        }

        if ($car->dismissed === 'Да') {
            throw new Exception('Автомобиль с заданным ID уволен', 400);
        }

        $company = $car->company;
        if ($company->dismissed === 'Да') {
            $message = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
            throw new Exception($message, 401);
        }
    }
}
