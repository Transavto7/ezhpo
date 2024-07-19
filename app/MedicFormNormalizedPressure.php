<?php

namespace App;

use App\ValueObjects\Tonometer;
use Illuminate\Database\Eloquent\Model;

class MedicFormNormalizedPressure extends Model
{
    public $timestamps = false;

    public static function store(int $formId, Tonometer $pressure)
    {
        $existModel = self::query()->where('form_id', $formId)->first();
        if (!$existModel) {
            $existModel = new self();
            $existModel->setAttribute('form_id', $formId);
        }

        $existModel->setAttribute('pressure', strval($pressure));

        $existModel->save();
    }

    public static function reset(int $formId)
    {
        self::query()->where('form_id', $formId)->delete();
    }
}
