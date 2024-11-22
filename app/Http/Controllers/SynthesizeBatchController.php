<?php

namespace App\Http\Controllers;

use App\Http\Requests\SynthesizeBatchRequest;
use App\Jobs\SynthesizeBatchJob;
use App\Models\Tts;
use App\Services\Tts\TtsService;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;

class SynthesizeBatchController extends Controller
{
    public function __construct(private ResponseFactory $responseFactory, private Dispatcher $dispatcher, private TtsService $service) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(SynthesizeBatchRequest $request)//: \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();

        $texts = $validatedData['texts'];
        $voice = $validatedData['voice'];

        
        //Se eliminan duplicados y se hashean 1 vez
        $texts =  collect($texts)
        ->map(fn($item) => strtolower($item))
        ->mapWithKeys(fn($item) => [$this->service->generateHash($item,$voice)=>$item])
        ->unique();

        $foundDB = Tts::whereIn('hash', $texts->keys())
        ->select('hash') // Solo traer la columna 'hash'
        ->get()
        ->pluck('hash');
    
      
        $texts = $texts->reject(fn($value,$hash)=>$foundDB->contains($hash));
        

        if($texts->isNotEmpty()){
            if($texts->count()<5){
                logs()->info("1 solo worker");
                $this->dispatcher->dispatch(new SynthesizeBatchJob($texts, $voice));
            }else{
                logs()->info("Aprovechando todos los workers");
               foreach ($texts->split(5) as $groupTexts) {
                $this->dispatcher->dispatch(new SynthesizeBatchJob($groupTexts, $voice));
               }
            }
        }
        

        return $this->responseFactory->json([
            'message' => 'Success',
        ], 200);
    }
}