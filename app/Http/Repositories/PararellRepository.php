<?php

namespace App\Http\Repositories;

use App\Jobs\ParalellJob;
use App\Util\ListUtil;
use Illuminate\Support\Facades\Log;

class PararellRepository
{


    public function snedGretings(string $text)
    {
        Log::info("Hola desd eel repositorio :)");
        Log::info("Procesando texto: " . $text);
    }
}
