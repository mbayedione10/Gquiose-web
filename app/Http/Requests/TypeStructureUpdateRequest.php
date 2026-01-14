<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TypeStructureUpdateRequest extends FormRequest
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
                Rule::unique('type_structures', 'name')->ignore(
                    $this->typeStructure
                ),
                'max:255',
                'string',
            ],
            'icon' => ['required', 'max:255', 'string'],
            'status' => ['required', 'boolean'],
        ];
    }
}
