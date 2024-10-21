<?php

namespace App\Repositories\AnketaVerifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AnketaVerificationsRepository
{
    /**
     * @param string $anketaUuid
     * @return array[]
     */
    public function findVerificationDatesByUuid(string $anketaUuid): array
    {
        return DB::table('anketa_verifications as av')
            ->select([
                'av.verification_date',
                'av.client_hash'
            ])
            ->where('av.anketa_uuid', '=', $anketaUuid)
            ->get()
            ->map(function ($item) {
                return [
                    'verification_date' => Carbon::parse($item->verification_date),
                    'client_hash' => $item->client_hash,
                ];
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
