<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchLocationRequest extends FormRequest
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
            'query' => 'required|string|min:2',
            'limit' => 'sometimes|integer|min:1|max:10'
        ];
    }

    public function messages(): array
    {
        return [
            'query.required' => 'Поисковый запрос обязателен',
            'query.min' => 'Поисковый запрос должен содержать не менее 2 символов',
            'limit.integer' => 'Лимит должен быть целым числом',
            'limit.min' => 'Лимит должен быть не менее 1',
            'limit.max' => 'Лимит не может превышать 10',
        ];
    }
}
