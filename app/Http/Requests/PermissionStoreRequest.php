<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PermissionStoreRequest extends FormRequest
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
                'unique:permissions,name',
                'max:255',
                'string',
            ],
            'label' => [
                'required',
                'unique:permissions,label',
                'max:255',
                'string',
            ],
            'type' => ['required', 'max:255', 'string'],
        ];
    }
}
