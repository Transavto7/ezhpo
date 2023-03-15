<?php

namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{

    public function index()
    {
        return view('admin.settings', [
            'logo' => Settings::setting('logo') ?? null,
            'sms_api_key' => Settings::setting('sms_api_key') ?? null,
            'sms_text_phone' => Settings::setting('sms_text_phone') ?? null,
            'sms_text_driver' => Settings::setting('sms_text_driver') ?? null,
            'sms_text_car' => Settings::setting('sms_text_driver') ?? null,
            'sms_text_default' => Settings::setting('sms_text_default') ?? null,
            'id_auto' => Settings::setting('id_auto') ?? null,
            'id_auto_required' => Settings::setting('id_auto_required') ?? null,
            'phone' => Settings::setting('phone') ?? null,
            'telegram' => Settings::setting('telegram') ?? null,
            'pressure_systolic' => Settings::setting('pressure_systolic') ?? null,
            'pressure_diastolic' => Settings::setting('pressure_diastolic') ?? null,
            'timeout' => Settings::setting('timeout') ?? null
        ]);
    }

    public function update(Request $request)
    {
        if ($request->hasFile('logo')) {
            $logo = Settings::where('key', 'logo')->first();
            Storage::disk('public')->delete($logo->value);
            $path = Storage::disk('public')->putFile('static', $request->file('logo'));
            $logo->value = $path;
            $logo->save();
        }

        Settings::set('sms_api_key', $request->sms_api_key);
        Settings::set('sms_text_phone', $request->sms_text_phone);
        Settings::set('sms_text_driver', $request->sms_text_driver);
        Settings::set('sms_text_car', $request->sms_text_car);
        Settings::set('sms_text_default', $request->sms_text_default);
        Settings::set('phone', $request->phone);
        Settings::set('telegram', $request->telegram);
        Settings::set('pressure_systolic', $request->pressure_systolic);
        Settings::set('pressure_diastolic', $request->pressure_diastolic);
        Settings::set('timeout', $request->timeout);

        $id_auto = (bool) $request->id_auto;
        Settings::set('id_auto', $id_auto ? '1' : '0');

        $id_auto_required = (bool) $request->id_auto_required;
        Settings::set('id_auto_required', $id_auto_required ? '1' : '0');

        return back();
    }
}
