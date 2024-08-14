<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSdpoCrashRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uuid' => 'required|string|size:36|unique:sdpo_crash_logs,uuid',
            'type' => 'required|string|min:1|max:255',
            'version' => 'required|string|min:5|max:255',
            'happened_at' => 'required|date',
            'data' => 'nullable|json',
        ];
    }
}
