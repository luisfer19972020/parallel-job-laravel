<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SynthesizeRequest extends FormRequest
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
            'text'  => ['string', 'max:2000', 'min:1', 'required'],
            'voice' => ['required', 'in:ES-PP-FEMALE-01', 'string'],
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'text.string'    => 'Cada texto debe ser una cadena de caracteres.',
            'text.max'       => 'Cada texto no debe tener más de 2000 caracteres.',
            'text.min'       => 'Cada texto no debe tener menos de 5 caracteres.',
            'text.required'  => 'El texto es obligatorio.',
            'voice.required' => 'La voz es obligatoria.',
            'voice.in'       => 'No existe soporte para la voz enviada.',
        ];
    }
}