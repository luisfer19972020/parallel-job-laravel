<?php

namespace App\Services\Tts;

use App\Support\PiperTtsClient;

class PiperTtsProvider implements TtsProviderInterface
{
    public function synthesize(string $text, string $voice): array
    {
        $piperService = new PiperTtsClient("", "");
        $result       = $piperService->synthesize($text, $voice);

        return $result;
    }

    public function getFileExtension(): string
    {
        return 'wav';
    }

    public function getContentType(): string
    {
        return 'audio/wav';
    }
}