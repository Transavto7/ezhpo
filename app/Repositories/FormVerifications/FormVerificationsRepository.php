<?php

namespace App\Repositories\FormVerifications;

use App\Enums\FormVerificationStatus;
use App\Repositories\FormVerifications\Entities\FormVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class FormVerificationsRepository
{
    /**
     * @param string $formUuid
     * @return FormVerification[]
     */
    public function findVerificationDatesByUuid(string $formUuid): array
    {
        return DB::table('anketa_verifications')
            ->select([
                'id',
                'anketa_uuid',
                'verification_date',
                'client_hash',
                'verification_status',
            ])
            ->where('anketa_uuid', '=', $formUuid)
            ->get()
            ->map(function ($item) {
                return new FormVerification(
                    $item->id,
                    $item->anketa_uuid,
                    Carbon::parse($item->verification_date),
                    $item->client_hash,
                    FormVerificationStatus::fromString($item->verification_status),
                );
            })
            ->toArray();
    }

    /**
     * @param string $formUuid
     * @param string $clientHash
     * @return FormVerification[]
     */
    public function findVerificationsByParams(string $formUuid, string $clientHash): array
    {
        return DB::table('anketa_verifications')
            ->select([
                'id',
                'anketa_uuid',
                'verification_date',
                'client_hash',
                'verification_status',
            ])
            ->where('anketa_uuid', '=', $formUuid)
            ->where('client_hash', '=', $clientHash)
            ->get()
            ->map(function ($item) {
                return new FormVerification(
                    $item->id,
                    $item->anketa_uuid,
                    Carbon::parse($item->verification_date),
                    $item->client_hash,
                    FormVerificationStatus::fromString($item->verification_status),
                );
            })
            ->toArray();
    }

    public function addFormVerification(
        string                 $formUuid,
        string                 $clientHash,
        Carbon                 $verificationDate,
        FormVerificationStatus $verificationStatus
    ): void
    {
        DB::table('anketa_verifications')->insert([
            'anketa_uuid' => $formUuid,
            'client_hash' => $clientHash,
            'verification_date' => $verificationDate,
            'verification_status' => $verificationStatus->value(),
        ]);
    }
}
