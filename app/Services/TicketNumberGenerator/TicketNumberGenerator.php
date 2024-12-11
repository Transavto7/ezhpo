<?php

namespace App\Services\TicketNumberGenerator;

use Exception;

final class TicketNumberGenerator
{
    /**
     * @param string $companyId
     * @return int
     * @throws Exception
     */
    public function generateWithSettings(string $companyId): int
    {
        $tries = 0;

        do {
            $value = mt_rand(
                config('app.ticket_number.min'),
                config('app.ticket_number.max')
            );

            if (TicketNumberValidator::validate($companyId)($value)) {
                return $value;
            }

            $tries++;

            if ($tries > config('app.ticket_number.tries')) {
                throw new Exception('Превышен лимит попыток генерации номера ПЛ');
            }
        } while (true);
    }
}
