<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StructureUpdateRequest extends FormRequest
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
            'name' => ['required', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'phone' => [
                'required',
                Rule::unique('structures', 'phone')->ignore($this->structure),
                'max:255',
                'string',
            ],
            'type_structure_id' => ['required', 'exists:type_structures,id'],
            'status' => ['required', 'boolean'],
            'ville_id' => ['required', 'exists:villes,id'],
            'adresse' => ['required', 'max:255', 'string'],
        ];
    }
}
