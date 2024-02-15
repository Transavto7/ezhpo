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
            'date_end_check' => $action->getDateCheck()->copy()->addYear()
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
            ->whereNull('deleted_at')
            ->get();

        if ($terminals->count()) {
            $terminal = $terminals[0]->user;

            $message = 'Указанный серийный номер терминала уже используется';

            if ($terminal) {
                $message .= '<br><br>Терминал: ' . $terminal->hash_id . ' (' . $terminal->name . ')';
            }

            throw new Exception($message);
        }
    }

    private function validateDateCheck(Carbon $dateCheck): bool
    {
        return $dateCheck->lessThanOrEqualTo(Carbon::now());
    }
}
