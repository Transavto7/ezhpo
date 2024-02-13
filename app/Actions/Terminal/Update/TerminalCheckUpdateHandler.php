<?php

namespace App\Actions\Terminal\Update;

use App\Actions\Terminal\Update\Dto\TerminalCheckUpdateAction;
use App\TerminalCheck;
use App\User;
use Carbon\Carbon;
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
        $this->validateSerialNumberUnique($action->getSerialNumber(), $action->getUserId());

        if (!$this->validateDateCheck($action->getDateCheck())) {
            throw new Exception('Дата поверки должна быть датой не позже сегодняшней даты или равной ей');
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

    /**
     * @param string $serialNumber
     * @param string $userId
     * @return void
     * @throws Exception
     */
    private function validateSerialNumberUnique(string $serialNumber, string $userId)
    {
        $terminals = TerminalCheck::query()
            ->where('serial_number', '=', $serialNumber)
            ->where('user_id', '!=', $userId)
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
