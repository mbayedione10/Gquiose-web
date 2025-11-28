<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AlerteStoreRequest extends FormRequest
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
            'ref' => ['required', 'unique:alertes,ref', 'max:255', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'type_alerte_id' => ['required', 'exists:type_alertes,id'],
            'etat' => ['required', 'max:255', 'string'],
            'ville_id' => ['required', 'exists:villes,id'],
            'utilisateur_id' => ['required', 'exists:utilisateurs,id'],

            // Preuves (evidence files) - maximum 5 fichiers
            'preuves' => ['nullable', 'array', 'max:5'],
            'preuves.*' => [
                'file',
                'max:10240', // 10 MB max par fichier
                'mimes:jpeg,jpg,png,pdf,mp4,mov,avi,doc,docx'
            ],

            // Les conseils de sécurité seront générés automatiquement
            'conseils_lus' => ['nullable', 'boolean'],
        ];
    }
}
