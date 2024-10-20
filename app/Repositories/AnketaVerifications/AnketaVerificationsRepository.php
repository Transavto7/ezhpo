<?php

namespace App\Repositories\AnketaVerifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AnketaVerificationsRepository
{
    /**
     * @param string $anketaUuid
     * @return Carbon[]
     */
    public function findVerificationDatesByUuid(string $anketaUuid): array
    {
        return DB::table('anketa_verifications as av')
            ->select([
                'av.verification_date',
            ])
            ->where('av.anketa_uuid', '=', $anketaUuid)
            ->pluck('av.verification_date')
            ->map(function (string $date) {
                return Carbon::parse($date);
            })
            ->toArray();
    }

    public function findVerificationsByParams(string $anketaUuid, string $clientHash): array
    {
        return DB::table('anketa_verifications as av')
            ->select([
                'av.id',
                'av.anketa_uuid',
                'av.verification_date',
                'av.client_hash',
            ])
            ->where('av.anketa_uuid', '=', $anketaUuid)
            ->where('av.client_hash', '=', $clientHash)
            ->get()
            ->toArray();
    }

    public function addAnketVerification(string $anketaUuid, string $clientHash)
    {
        DB::table('anketa_verifications')->insert([
            'anketa_uuid' => $anketaUuid,
            'client_hash' => $clientHash,
            'verification_date' => Carbon::now(),
        ]);
    }
}
