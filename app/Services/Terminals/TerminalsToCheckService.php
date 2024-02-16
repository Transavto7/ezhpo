<?php

namespace App\Services\Terminals;

use App\TerminalCheck;
use Carbon\Carbon;

final class TerminalsToCheckService
{
    /**
     * @return array{
     *     'less_month': array,
     *     'expired': array,
     * }
     */
    public function getIds(): array
    {
        $terminalChecks = TerminalCheck::all();

        $lessMonth = $terminalChecks
            ->filter(function (TerminalCheck $terminalCheck) {
                if (!$terminalCheck->user) {
                    return false;
                }

                if (Carbon::now()->isSameDay($terminalCheck->date_end_check)) {
                    return true;
                }

                return Carbon::now()->lessThanOrEqualTo($terminalCheck->date_end_check) &&
                    Carbon::now()->diffInDays($terminalCheck->date_end_check) <= 30 &&
                    $terminalCheck->user;
            })
            ->map(function (TerminalCheck $terminalCheck) {
                return $terminalCheck->user->hash_id;
            })
            ->values();

        $expired = $terminalChecks
            ->filter(function (TerminalCheck $terminalCheck) {
                if (!$terminalCheck->user) {
                    return false;
                }

                if (Carbon::now()->isSameDay($terminalCheck->date_end_check)) {
                    return false;
                }

                return Carbon::now()->greaterThan($terminalCheck->date_end_check);
            })
            ->map(function (TerminalCheck $terminalCheck) {
                return $terminalCheck->user->hash_id;
            })
            ->values();

        return [
            'less_month' => $lessMonth,
            'expired' => $expired
        ];
    }
}
