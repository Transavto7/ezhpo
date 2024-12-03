<?php

namespace App\Contractcs;

use App\Actions\Reports\Journal\GetJournalData\GetJournalDataAction;

interface GetServicesReportForCompanyByPeriodInterface
{
    public function handle(GetJournalDataAction $action): array;
}
