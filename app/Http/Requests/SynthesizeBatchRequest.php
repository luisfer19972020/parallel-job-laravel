<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SynthesizeBatchRequest extends FormRequest
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
            'texts'   => ['required', 'array', 'min:1', 'max:100'],
            'texts.*' => ['string', 'max:2000', 'min:1'],
            'voice'   => ['required', 'in:ES-PP-FEMALE-01', 'string'],
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'texts.required' => 'Los textos son obligatorios.',
            'texts.array'    => 'Los textos deben de venir en un array.',
            'texts.min'      => 'El array de textos debe de contener al menos 1 texto.',
            'texts.max'      => 'el array de textos no puede contener más de 100 textos.',
            'texts.*.string' => 'Cada texto debe ser una cadena de caracteres.',
            'texts.*.max'    => 'Cada texto no debe tener más de 2000 caracteres.',
            'texts.*.min'    => 'Cada texto no debe tener menos de 5 caracteres.',
            'voice.required' => 'La voz es obligatoria.',
            'voice.in'       => 'No existe soporte para la voz enviada.',
        ];
    }
}