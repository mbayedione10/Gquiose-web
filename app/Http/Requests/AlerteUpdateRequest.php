<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AlerteUpdateRequest extends FormRequest
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
            'ref' => [
                'required',
                Rule::unique('alertes', 'ref')->ignore($this->alerte),
                'max:255',
                'string',
            ],
            'description' => ['required', 'max:255', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'type_alerte_id' => ['required', 'exists:type_alertes,id'],
            'etat' => ['required', 'max:255', 'string'],
            'ville_id' => ['required', 'exists:villes,id'],
        ];
    }
}
