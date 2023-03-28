<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkReportFilterRequest extends FormRequest
{
    /**
     * @var array
     */
    protected array $routeParametersToValidate = [];
    /**
     * @var array
     */
    protected array $queryParametersToValidate = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'dateFrom' => 'nullable|date|date_format:Y-m-d',
            'dateTo' => 'nullable|date|date_format:Y-m-d',
            'pvId' => 'nullable|integer|exists:points,id',
            'userId' => 'nullable|integer|exists:users,id'
        ];
    }

    public function all($keys = null): array
    {
        $data = parent::all();

        foreach ($this->routeParametersToValidate as $validationDataKey => $routeParameter) {
            $data[$validationDataKey] = $this->route($routeParameter);
        }

        foreach ($this->queryParametersToValidate as $validationDataKey => $queryParameter) {
            $data[$validationDataKey] = $this->query($queryParameter);
        }

        return $data;
    }
}
