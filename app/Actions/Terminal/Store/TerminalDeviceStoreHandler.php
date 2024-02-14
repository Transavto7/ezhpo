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
        $this->validateDevicesUnique($action->getDeviceSerialNumber());

        $terminalDevice = TerminalDevice::create([
            'user_id' => $action->getUserId(),
            'device_name' => $action->getDeviceName(),
            'device_serial_number' => $action->getDeviceSerialNumber()
        ]);

        return $terminalDevice->id;
    }

    /**
     * @param string $serialNumber
     * @return void
     * @throws Exception
     */
    private function validateDevicesUnique(string $serialNumber)
    {
        $terminalDevices = TerminalDevice::query()
            ->where('device_serial_number','=', $serialNumber)
            ->whereNull('deleted_at')
            ->get();

        if ($terminalDevices->count()) {
            $terminal = $terminalDevices[0]->user;

            $message = 'Указанный серийный номер оборудования уже используется';

            if ($terminal) {
                $message .= '<br><br>Терминал: ' . $terminal->hash_id . ' (' . $terminal->name . ')';
            }

            throw new Exception($message);
        }

    }
}
