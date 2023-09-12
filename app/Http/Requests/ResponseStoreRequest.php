<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResponseStoreRequest extends FormRequest
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
            'question_id' => ['required', 'exists:questions,id'],
            'reponse' => ['required', 'max:255', 'string'],
            'isValid' => ['required', 'boolean'],
            'utilisateur_id' => ['required', 'exists:utilisateurs,id'],
        ];
    }
}
