<?php

namespace App\Repositories\AnketaVerifications;

use App\Enums\AnketaVerificationStatus;
use App\Repositories\AnketaVerifications\Entities\AnketaVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AnketaVerificationsRepository
{
    /**
     * @param string $anketaUuid
     * @return AnketaVerification[]
     */
    public function findVerificationDatesByUuid(string $anketaUuid): array
    {
        return DB::table('anketa_verifications')
            ->select([
                'id',
                'anketa_uuid',
                'verification_date',
                'client_hash',
                'verification_status',
            ])
            ->where('anketa_uuid', '=', $anketaUuid)
            ->get()
            ->map(function ($item) {
                return new AnketaVerification(
                    $item->id,
                    $item->anketa_uuid,
                    Carbon::parse($item->verification_date),
                    $item->client_hash,
                    AnketaVerificationStatus::fromString($item->verification_status),
                );
            })
            ->toArray();
    }

    /**
     * @param string $anketaUuid
     * @param string $clientHash
     * @return AnketaVerification[]
     */
    public function findVerificationsByParams(string $anketaUuid, string $clientHash): array
    {
        return DB::table('anketa_verifications')
            ->select([
                'id',
                'anketa_uuid',
                'verification_date',
                'client_hash',
                'verification_status',
            ])
            ->where('anketa_uuid', '=', $anketaUuid)
            ->where('client_hash', '=', $clientHash)
            ->get()
            ->map(function ($item) {
                return new AnketaVerification(
                    $item->id,
                    $item->anketa_uuid,
                    Carbon::parse($item->verification_date),
                    $item->client_hash,
                    AnketaVerificationStatus::fromString($item->verification_status),
                );
            })
            ->toArray();
    }

    public function addAnketVerification(
        string $anketaUuid,
        string $clientHash,
        Carbon $verificationDate,
        AnketaVerificationStatus $verificationStatus
    ): void
    {
        DB::table('anketa_verifications')->insert([
            'anketa_uuid' => $anketaUuid,
            'client_hash' => $clientHash,
            'verification_date' => $verificationDate,
            'verification_status' => $verificationStatus->value(),
        ]);
    }
}
