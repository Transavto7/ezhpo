<?php

namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{

    public function index()
    {
        $settings = Settings::all();

        return view('admin.settings', [
            'logo' => $settings->where('key', 'logo')->first(),
            'sms_api_key' => $settings->where('key', 'sms_api_key')->first(),
            'sms_text_phone' => $settings->where('key', 'sms_text_phone')->first(),
            'sms_text_driver' => $settings->where('key', 'sms_text_driver')->first(),
            'sms_text_car' => $settings->where('key', 'sms_text_car')->first(),
            'sms_text_default' => $settings->where('key', 'sms_text_default')->first(),
            'id_auto' => $settings->where('key', 'id_auto')->first(),
            'id_auto_required' => $settings->where('key', 'id_auto_required')->first(),
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

        Settings::where('key', 'sms_api_key')->update(['value' => $request->sms_api_key]);
        Settings::where('key', 'sms_text_phone')->update(['value' => $request->sms_text_phone]);
        Settings::where('key', 'sms_text_driver')->update(['value' => $request->sms_text_driver]);
        Settings::where('key', 'sms_text_car')->update(['value' => $request->sms_text_car]);
        Settings::where('key', 'sms_text_default')->update(['value' => $request->sms_text_default]);

        $id_auto = (bool) $request->id_auto;
        Settings::where('key', 'id_auto')->update(['value' => $id_auto ? '1' : '0']);

        $id_auto_required = (bool) $request->id_auto_required;
        Settings::where('key', 'id_auto_required')->update(['value' => $id_auto_required ? '1' : '0']);

        return back();
    }
}
