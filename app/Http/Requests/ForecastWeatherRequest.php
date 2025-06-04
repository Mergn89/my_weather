<?php

namespace App\Http\Requests;

class ForecastWeatherRequest extends BaseWeatherRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'cnt' => 'sometimes|integer|min:1|max:40',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'cnt.integer' => 'Количество должно быть целым числом',
            'cnt.min' => 'Количество должно быть не менее 1',
            'cnt.max' => 'Количество не может превышать 40',
        ]);
    }
}
