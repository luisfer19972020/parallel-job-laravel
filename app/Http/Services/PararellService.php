<?php

namespace App\Http\Services;

use App\Jobs\ParalellJob;
use App\Util\ListUtil;
use Illuminate\Support\Facades\Log;

class PararellService
{

    const LIMITE = 10;

    public function processTexts(array $texts)
    {
        Log::info("Inicio del procesamiento");
        foreach (ListUtil::splitListByLimit($texts, self::LIMITE) as $key => $lote) {
            Log::info("Procesando el lote: " . ($key + 1) . " con valores: " . implode(",", $lote));
            foreach ($lote as $text) {
                ParalellJob::dispatch($text);
            }
        }
        Log::info("Procesado correctamente");
    }
}
