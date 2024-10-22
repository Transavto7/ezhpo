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
        $terminalChecks = TerminalCheck::query()
            ->select([
                'terminal_checks.date_end_check',
                'users.hash_id as user_hash_id'
            ])
            ->leftJoin('users', 'terminal_checks.user_id', '=', 'users.id')
            ->get();

        $now = Carbon::now();

        $lessMonth = $terminalChecks
            ->filter(function (TerminalCheck $terminalCheck) use ($now) {
                if (!$terminalCheck->user_hash_id) {
                    return false;
                }

                if ($now->isSameDay($terminalCheck->date_end_check)) {
                    return true;
                }

                return $now->lessThanOrEqualTo($terminalCheck->date_end_check) &&
                    $now->diffInDays($terminalCheck->date_end_check) <= 30;
            })
            ->map(function (TerminalCheck $terminalCheck) {
                return $terminalCheck->user_hash_id;
            })
            ->values();

        $expired = $terminalChecks
            ->filter(function (TerminalCheck $terminalCheck) use ($now) {
                if (!$terminalCheck->user_hash_id) {
                    return false;
                }

                if ($now->isSameDay($terminalCheck->date_end_check)) {
                    return false;
                }

                return $now->greaterThan($terminalCheck->date_end_check);
            })
            ->map(function (TerminalCheck $terminalCheck) {
                return $terminalCheck->user_hash_id;
            })
            ->values();

        return [
            'less_month' => $lessMonth,
            'expired' => $expired
        ];
    }
}
