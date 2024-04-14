<?php

namespace App\Actions\Terminal\Store;

use App\Actions\Terminal\Store\Dto\TerminalCheckStoreAction;
use App\TerminalCheck;
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

        //TODO: валидация дат?

        $terminalCheck = TerminalCheck::create([
            'user_id' => $action->getUserId(),
            'serial_number' => $action->getSerialNumber(),
            'date_check' => $action->getDateCheck(),
            'date_end_check' => $action->getDateCheck()->copy()->addYear(),
            'date_service_start' => $action->getDateServiceStart(),
            'date_service_end' => $action->getDateServiceEnd(),
            'failures_count' => $action->getFailuresCount()
        ]);

        return $terminalCheck->id;
    }

    private function validateUserUnique(int $userId): bool
    {
        return !TerminalCheck::query()
            ->where('user_id', '=', $userId)
            ->whereNull('deleted_at')
            ->get()
            ->count();
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
