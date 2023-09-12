<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UtilisateurStoreRequest extends FormRequest
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
            'nom' => ['required', 'max:255', 'string'],
            'prenom' => ['required', 'max:255', 'string'],
            'email' => ['required', 'unique:utilisateurs,email', 'email'],
            'phone' => [
                'required',
                'unique:utilisateurs,phone',
                'max:255',
                'string',
            ],
            'sexe' => ['required', 'max:255', 'string'],
            'status' => ['required', 'boolean'],
        ];
    }
}
