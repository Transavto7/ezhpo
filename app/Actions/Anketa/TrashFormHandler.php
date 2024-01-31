<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Driver;
use Carbon\Carbon;

final class TrashFormHandler
{
    public function handle(Anketa $anketa, $action): bool
    {
        $anketa->in_cart = $action;
        if ($anketa->type_anketa === 'medic' && $anketa->driver_id) {
            $driver = Driver::where('hash_id', $anketa->driver_id)->first();

            if ($driver && $driver->end_of_ban) {
                $last = Anketa::orderBy('created_at', 'desc')
                    ->where('driver_id', $anketa->driver_id)
                    ->select('driver_id', 'created_at', 'id')->first();

                if ($last->id === $anketa->id) {
                    $driver->end_of_ban = null;
                    $driver->save();
                }
            }
        }
        $anketa->deleted_id = user()->id;
        $anketa->deleted_at = Carbon::now();

        return $anketa->save();
    }
}
