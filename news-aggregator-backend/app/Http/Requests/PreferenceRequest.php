<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sources' => 'array',
            'sources.*' => 'integer|exists:sources,id',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
            'authors' => 'array',
            'authors.*' => 'string'
        ];
    }
}
