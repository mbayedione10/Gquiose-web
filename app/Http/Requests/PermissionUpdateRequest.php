<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PermissionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('permissions', 'name')->ignore($this->permission),
                'max:255',
                'string',
            ],
            'label' => [
                'required',
                Rule::unique('permissions', 'label')->ignore($this->permission),
                'max:255',
                'string',
            ],
            'type' => ['required', 'max:255', 'string'],
        ];
    }
}
