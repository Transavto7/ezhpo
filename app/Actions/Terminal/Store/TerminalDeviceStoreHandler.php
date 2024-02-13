<?php

namespace App\Actions\Terminal\Store;

use App\Actions\Terminal\Store\Dto\TerminalDeviceStoreAction;
use App\TerminalDevice;
use Exception;

final class TerminalDeviceStoreHandler
{
    /**
     * @param TerminalDeviceStoreAction $action
     * @return int|mixed
     * @throws Exception
     */
    public function handle(TerminalDeviceStoreAction $action)
    {
        if (!$this->validateDevicesUnique($action->getDeviceSerialNumber())) {
            throw new Exception('Указанный серийный номер оборудования уже используется');
        }

        $terminalDevice = TerminalDevice::create([
            'user_id' => $action->getUserId(),
            'device_name' => $action->getDeviceName(),
            'device_serial_number' => $action->getDeviceSerialNumber()
        ]);

        return $terminalDevice->id;
    }

    private function validateDevicesUnique(string $serialNumber): bool
    {
        return !TerminalDevice::where('device_serial_number','=', $serialNumber)->get()->count();
    }
}
