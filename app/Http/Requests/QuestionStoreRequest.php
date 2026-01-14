<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionStoreRequest extends FormRequest
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
                'unique:questions,name',
                'max:255',
                'string',
            ],
            'reponse' => ['required', 'max:255', 'string'],
            'option1' => ['required', 'max:255', 'string'],
            'status' => ['required', 'boolean'],
            'thematique_id' => ['required', 'exists:thematiques,id'],
        ];
    }
}
