<?php

namespace App\Http\Controllers;

use App\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function ImportSystemSettings () {
        $settings = new SystemSetting();
        $settings = $settings->settings;

        foreach($settings as $setting) {
            if(SystemSetting::where('param', $setting['param'])->count() <= 0) {
                $setting['val'] = '';
                SystemSetting::create($setting);
            }
        }
    }

    public function RenderSystemSettings () {
        $settings = SystemSetting::all();

        return view('admin.settings', [
            'settings' => $settings
        ]);
    }

    public function UpdateSystemSetting (Request $request) {
        $data = $request->all();
        $fields = SystemSetting::all();

        foreach($fields as $field) {
            if(!isset($data[$field->param])) {
                $field->val = '';
                $field->save();
            }
        }

        foreach($data as $k => $v) {
            $field = SystemSetting::where('param', $k)->first();

            if($field) {
                $field->val = $v;

                $field->save();
            }
        }

        return back();
    }
}
