<?php

namespace App\Services\Tts;

use App\Enums\TtsState;
use App\Models\repository\IFileRepository;
use App\Models\repository\FileRepository;



class FileService implements IFileService
{
    protected string $storageDisk;

    public function __construct()
    {
        $this->storageDisk = 'local';
    }

    public function getAudioWavDurationInMileSeconds(string $fileData): string|float
    {
        if (! $fileData)
        {
            return 'No se pudo cargar el archivo WAV.';
        }
        if (substr($fileData, 0, 4) !== 'RIFF' || substr($fileData, 8, 4) !== 'WAVE')
        {
            return 'El archivo no es un archivo WAV válido.';
        }

        $byteRate = unpack('V', substr($fileData, 28, 4))[1];
        $dataSize = unpack('V', substr($fileData, 40, 4))[1];
        $duration = $dataSize / $byteRate * 1000;

        return $duration;
    }

}