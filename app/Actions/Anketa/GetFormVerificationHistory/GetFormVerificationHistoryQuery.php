<?php

namespace App\Actions\Anketa\GetFormVerificationHistory;

use App\Repositories\FormVerifications\FormVerificationsRepository;
use App\Repositories\FormVerifications\Entities\FormVerification;
use App\ViewModels\FormVerificationDetails\FormVerificationHistoryItem;

final class GetFormVerificationHistoryQuery
{
    /**
     * @var FormVerificationsRepository
     */
    private $repository;

    /**
     * @param FormVerificationsRepository $repository
     */
    public function __construct(FormVerificationsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(GetFormVerificationHistoryParams $params): array
    {
        $verifications = $this->repository->findVerificationDatesByUuid($params->getFormUuid());
        return array_map(function (FormVerification $item) use ($params) {
            return new FormVerificationHistoryItem(
                $item->getVerificationDate(),
                $params->getClientHash() === $item->getClientHash()
            );
        }, $verifications);
    }
}
