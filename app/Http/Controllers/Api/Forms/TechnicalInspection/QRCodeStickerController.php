<?php

namespace App\Http\Controllers\Api\Forms\TechnicalInspection;

use App\Car;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Enums\QRCodeLinkParameter;
use App\Services\QRCode\QRCodeLinkGenerator;
use App\Services\QRCode\QRCodeStickerGenerator;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Carbon;

class QRCodeStickerController
{
    const DRIVER = 'DRIVER';
    const CAR = 'CAR';

    /**
     * @throws Exception
     */
    public function __invoke(Request $request)
    {
        $entityId = $request->input('id');
        $type = $request->input('type');
        $user = $request->user('api');

        $this->checkUser($user);

        if ($type === QRCodeLinkParameter::DRIVER_ID) {
            $this->checkDriver($user, $entityId);
        } elseif ($type === QRCodeLinkParameter::CAR_ID) {
            $this->checkCar($user, $entityId);
        } else {
            throw new \DomainException('Тип сущности не указан или указан неверно. Тип: '.$type);
        }

        $img = new QRCodeStickerGenerator(
            new QRCodeLinkGenerator(
                $request->input('id'),
                QRCodeLinkParameter::fromString($request->input('type'))
            )
        );

        return $this->getPdf($entityId, $type, $img);
    }

    /**
     * @throws Exception
     */
    private function checkUser(User $user)
    {
        if ($user->blocked) {
            throw new Exception('Этот терминал заблокирован!', 400);
        }
    }

    /**
     * @throws Exception
     */
    private function checkDriver(User $user, string $driverId)
    {
        $driver = Driver::where('hash_id', $driverId)->first();
        if (!$driver) {
            throw new Exception('Указанный водитель не найден', 400);
        }

        date_default_timezone_set('UTC');
        $time = time();
        $apiClient = $user;
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
    private function checkCar(User $user, string $carId)
    {
        $car = Car::where('hash_id', $carId)->first();
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

    private function getUrl()
    {
        $url = env('APP_URL');
        $http = 'http://';
        $https = 'https://';

        if (strpos($url, $http) !== false) {
            return substr($url,  strlen($http));
        }

        if (strpos($url, $https) !== false) {
            return substr($url,  strlen($https));
        }

        return $url;
    }

    private function getView(string $id, string $type, $img)
    {
        return view('templates.qr-code',
            [
                'qrCode' => $img->generate(),
                'id' => $id,
                'type' => $type === QRCodeLinkParameter::CAR_ID
                    ? self::CAR
                    : self::DRIVER,
                'domain' => $this->getUrl()
            ]
        );
    }

    private function getPdf(string $id, string $type, $img)
    {
        $customPaper = array(0,0,300.00,225.00);

        $pdf = Pdf::loadView('templates.qr-code', [
            'qrCode' => $img->generate(),
            'id' => $id,
            'type' => $type === QRCodeLinkParameter::CAR_ID
                ? self::CAR
                : self::DRIVER,
            'domain' => $this->getUrl()
        ])
            ->setPaper($customPaper, 'landscape');;

        $response = response()->make($pdf->output(), 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    }
}

