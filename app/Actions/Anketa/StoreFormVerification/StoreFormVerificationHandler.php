<?php

namespace App\Actions\Anketa\StoreFormVerification;

use App\Enums\FormVerificationStatus;
use App\Models\Forms\Form;
use App\Repositories\FormVerifications\FormVerificationsRepository;
use Http\Client\Common\Exception\HttpClientNotFoundException;

final class StoreFormVerificationHandler
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

    public function handle(StoreFormVerificationCommand $command)
    {
        $form = Form::withTrashed()->where('uuid', '=', $command->getFormUuid())->first();

        if (!$form) {
            throw new HttpClientNotFoundException();
        }

        $verificationStatus = FormVerificationStatus::verified();
        if ($form->deleted_at !== null) {
            $verificationStatus = FormVerificationStatus::deleted();
        }

        $existedVerifications = $this->repository->findVerificationsByParams(
            $command->getFormUuid(),
            $command->getClientHash()
        );

        if (!count($existedVerifications) && !$command->isAuthorized()) {
            $this->repository->addFormVerification(
                $command->getFormUuid(),
                $command->getClientHash(),
                $command->getDate(),
                $verificationStatus
            );
        }
    }
}
