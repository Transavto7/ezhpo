<?php

namespace App\Actions\Terminal\Store;

use App\Actions\Terminal\Store\Dto\TerminalCheckStoreAction;
use App\TerminalCheck;
use App\User;
use Carbon\Carbon;
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

        if (!$this->validateDateCheck($action->getDateCheck())) {
            throw new Exception('Дата поверки должна быть датой не позже сегодняшней даты или равной ей');
        }

        $this->validateSerialNumberUnique($action->getSerialNumber());

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

    /**
     * @param string $serialNumber
     * @return void
     * @throws Exception
     */
    private function validateSerialNumberUnique(string $serialNumber)
    {
        $terminals = TerminalCheck::query()
            ->where('serial_number', '=', $serialNumber)
            ->get();

        if ($terminals->count()) {
            $terminal = User::find($terminals[0]->user_id);

            $hashId = $terminal->hash_id;
            $name = $terminal->name;

            throw new Exception('Указанный серийный номер терминала уже используется.<br><br>Терминал: '.$name.' ('.$hashId.').');
        }
    }

    private function validateDateCheck(Carbon $dateCheck): bool
    {
        return $dateCheck->lessThanOrEqualTo(Carbon::now());
    }
}
