<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseWeatherRequest extends FormRequest
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
            'city' => 'sometimes|string|max:255',
            'lat' => 'sometimes|numeric|between:-90,90',
            'lon' => 'sometimes|numeric|between:-180,180',
            'units' => 'sometimes|in:metric,imperial',
        ];
    }

    public function messages(): array
    {
        return [
            'city.string' => 'Название города должно быть строкой',
            'city.max' => 'Название города не может превышать 255 символов',
            'lat.numeric' => 'Широта должна быть числом',
            'lat.between' => 'Широта должна быть в диапазоне от -90 до 90 градусов',
            'lon.numeric' => 'Долгота должна быть числом',
            'lon.between' => 'Долгота должна быть в диапазоне от -180 до 180 градусов',
            'units.in' => 'Единицы измерения должны быть metric или imperial',
        ];
    }
}
