<?php

namespace App\Actions\Reports\OneC\Create;

use App\Company;
use App\Models\Report;
use Exception;

class ReportHandler
{
    /**
     * @throws Exception
     */
    public function handle(ReportAction $action): Report
    {
        if (! $this->isCompanyValid($action->getPayload()->getCompanyId())) {
            throw new Exception('Компания не найдена');
        }

        $report = Report::create([
            'type' => $action->getType()->value(),
            'status' => $action->getStatus()->value(),
            'user_id' => $action->getUserId(),
            'payload' => [
                'date_to' => $action->getPayload()->getDateTo()->format('Y-m-d'),
                'date_from' => $action->getPayload()->getDateFrom()->format('Y-m-d'),
                'company_id' => $action->getPayload()->getCompanyId(),
            ],
        ]);

        $report->save();

        return $report;
    }

    private function isCompanyValid(string $companyId): bool
    {
        $company = Company::where('hash_id', $companyId);

        return $company->count() > 0;
    }
}
