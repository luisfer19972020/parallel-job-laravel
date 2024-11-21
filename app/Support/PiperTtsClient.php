<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class PiperTtsClient
{
    public function __construct(private string $url, private string $api_key) {}

    /**
     * Sintetiza el texto utilizando el servicio Piper-TTS.
     *
     * @param  string  $text  Texto a sintetizar.
     * @param  string  $model  Modelo de voz a utilizar.
     * @return array<string> Resultado de la síntesis.
     */
    public function synthesize(string $text, string $model): array
    {
        sleep(7);
       return [
        'status'=>'ok',
        'data'=>[
            'audio'=>'test'
        ]
       ];
    }
}