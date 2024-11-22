<?php

namespace App\Services\Tts;

interface TtsProviderInterface
{
    /**
     * @return array<string>
     */
    public function synthesize(string $text, string $voice): array;

    public function getFileExtension(): string;

    public function getContentType(): string;
}