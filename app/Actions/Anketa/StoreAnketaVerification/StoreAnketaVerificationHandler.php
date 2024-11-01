<?php

namespace App\Actions\Anketa\StoreAnketaVerification;

use App\Anketa;
use App\Enums\AnketaVerificationStatus;
use App\Repositories\AnketaVerifications\AnketaVerificationsRepository;
use Http\Client\Common\Exception\HttpClientNotFoundException;

final class StoreAnketaVerificationHandler
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

    public function handle(StoreAnketaVerificationCommand $command)
    {
        $anketa = Anketa::where('uuid', '=', $command->getAnketaUuid())->first();

        if (!$anketa) {
            throw new HttpClientNotFoundException();
        }

        $verificationStatus = AnketaVerificationStatus::verified();
        if ($anketa->in_cart === 1) {
            $verificationStatus = AnketaVerificationStatus::deleted();
        }

        $existedVerifications = $this->repository->findVerificationsByParams(
            $command->getAnketaUuid(),
            $command->getClientHash()
        );

        if (!count($existedVerifications) && !$command->isAuthorized()) {
            $this->repository->addAnketVerification(
                $command->getAnketaUuid(),
                $command->getClientHash(),
                $command->getDate(),
                $verificationStatus
            );
        }
    }
}
