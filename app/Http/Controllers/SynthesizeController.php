<?php

namespace App\Http\Controllers;

use App\Http\Requests\SynthesizeRequest;
use App\Services\Tts\ITtsService;
use App\Services\Tts\TtsService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SynthesizeController extends Controller
{
    private ITtsService $ttsService;
    public function __construct(TtsService $ttsService)
    {
        $this->ttsService = $ttsService;
    }

    public function __invoke(SynthesizeRequest $request): JsonResponse|StreamedResponse
    {
        $validatedData = $request->validated();

        $text = $validatedData['text'];
        //Aplica siempre el -RT pero esto solo aplica para Piper. cambiar cuando se agregen mas proveedores
        $voice = $validatedData['voice'].'-RT';

        $audioData = $this->ttsService->getAudio($text, $voice);

        if ($audioData === false)
        {
            logs()->error('Error generating audio.');

            return response()->json(['error' => 'Error generating audio'], 500);
        }
        logs()->info($audioData);
        return response()->json(['message' => $audioData], 200);


        // Set the appropriate content type and filename
     /*    $contentType = 'audio/wav'; // This is obtained by using the provider, tech debt.
        $filename    = 'output.wav';

        return new StreamedResponse(function () use ($audioData) {
            echo $audioData;
        }, 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]); */
    }
}