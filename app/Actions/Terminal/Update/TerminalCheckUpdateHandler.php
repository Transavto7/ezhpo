<?php

namespace App\Actions\Terminal\Update;

use App\Actions\Terminal\Update\Dto\TerminalCheckUpdateAction;
use App\TerminalCheck;
use Exception;

final class TerminalCheckUpdateHandler
{
    /**
     * @param TerminalCheckUpdateAction $action
     * @return int|mixed
     * @throws Exception
     */
    public function handle(TerminalCheckUpdateAction $action)
    {
        if (!$this->validateSerialNumberUnique($action->getSerialNumber(), $action->getUserId())) {
            throw new Exception('Указанный серийный номер терминала уже используется');
        }

        $terminalCheck = TerminalCheck::query()
            ->where('user_id', '=', $action->getUserId())
            ->get()
            ->first();

        $terminalCheck->update([
            'user_id' => $action->getUserId(),
            'serial_number' => $action->getSerialNumber(),
            'date_check' => $action->getDateCheck(),
        ]);

        return $terminalCheck->id;
    }

    private function validateSerialNumberUnique(string $serialNumber, string $userId): bool
    {
        return !TerminalCheck::query()
            ->where('serial_number', '=', $serialNumber)
            ->where('user_id', '!=', $userId)
            ->get()
            ->count();
    }
}
