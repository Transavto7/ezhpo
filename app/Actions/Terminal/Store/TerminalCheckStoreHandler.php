<?php

namespace App\Actions\Terminal\Store;

use App\Actions\Terminal\Store\Dto\TerminalCheckStoreAction;
use App\TerminalCheck;
use Exception;

final class TerminalCheckStoreHandler
{
    /**
     * @param TerminalCheckStoreAction $action
     * @return void
     * @throws Exception
     */
    public function handle(TerminalCheckStoreAction $action)
    {
        if (!$this->validateUserUnique($action->getUserId())) {
            throw new Exception('Для указанного терминала уже добавлена информация об оборудовании');
        }

        if (!$this->validateSerialNumberUnique($action->getSerialNumber())) {
            throw new Exception('Указанный серийный номер терминала уже используется');
        }

        $terminalCheck = TerminalCheck::create([
            'user_id' => $action->getUserId(),
            'serial_number' => $action->getSerialNumber(),
            'date_check' => $action->getDateCheck(),
        ]);

        return $terminalCheck->id;
    }

    private function validateUserUnique(int $userId): bool
    {
        return !TerminalCheck::where('user_id', '=', $userId)->get()->count();
    }

    private function validateSerialNumberUnique(string $serialNumber): bool
    {
        return !TerminalCheck::where('serial_number', '=', $serialNumber)->get()->count();
    }
}
