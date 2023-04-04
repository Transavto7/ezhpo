<?php
declare(strict_types=1);
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SaveSidebarMenuItem extends FormRequest
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
            'title' => 'string|required',
            'tooltip_prompt' => 'string|nullable',
            'parent_id' => 'integer|nullable',
            'route_name' => 'string|required',
            'access_permissions' => 'string|required'
        ];
    }
}
