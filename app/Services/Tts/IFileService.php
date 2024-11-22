<?php

namespace App\Services\Tts;

interface IFileService
{
    public function getAudioWavDurationInMileSeconds(string $filename): string|float;

}