<?php

namespace App\Enums;

enum TtsState: string
{
    case INIT     = 'INIT';
    case PROGRESS = 'PROGRESS';
    case OK       = 'OK';
    case ERROR    = 'ERROR';
}