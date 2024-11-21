<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SynthesizeResponseData extends Data
{
    public function __construct(
        public string $status,
        public ?string $message,
        public ?string $audio,
        public string $contentType,
        public string $extension
    ) {}
}