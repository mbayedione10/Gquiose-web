<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleStoreRequest extends FormRequest
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
            'title' => ['required', 'max:255', 'string'],
            'description' => ['required', 'max:255', 'string'],
            'rubrique_id' => ['required', 'exists:rubriques,id'],
            'slug' => ['required', 'unique:articles,slug', 'max:255', 'string'],
            'image' => ['nullable', 'image', 'max:1024'],
            'status' => ['required', 'boolean'],
            'user_id' => ['required', 'exists:users,id'],
            'video_url' => ['nullable', 'max:255', 'string'],
            'audio_url' => ['nullable', 'max:255', 'string'],
        ];
    }
}
