<?php

namespace App\Actions\Anketa\GetAnketaVerificationHistory;

use App\Repositories\AnketaVerifications\AnketaVerificationsRepository;
use App\Repositories\AnketaVerifications\Entities\AnketaVerification;
use App\ViewModels\AnketaVerificationDetails\AnketaVerificationHistoryItem;

final class GetAnketaVerificationHistoryQuery
{
    /**
     * @var AnketaVerificationsRepository
     */
    private $repository;

    /**
     * @param AnketaVerificationsRepository $repository
     */
    public function __construct(AnketaVerificationsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(GetAnketaVerificationHistoryParams $params)
    {
        $verifications = $this->repository->findVerificationDatesByUuid($params->getAnketaUuid());
        return array_map(function (AnketaVerification $item) use ($params) {
            return new AnketaVerificationHistoryItem(
                $item->getVerificationDate(),
                $params->getClientHash() === $item->getClientHash()
            );
        }, $verifications);
    }
}
