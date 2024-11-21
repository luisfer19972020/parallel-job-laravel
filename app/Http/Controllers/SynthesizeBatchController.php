<?php

namespace App\Http\Controllers;

use App\Http\Requests\SynthesizeBatchRequest;
use App\Jobs\SynthesizeBatchJob;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;

class SynthesizeBatchController extends Controller
{
    public function __construct(private ResponseFactory $responseFactory, private Dispatcher $dispatcher) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(SynthesizeBatchRequest $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validated();

        $texts = $validatedData['texts'];
        $voice = $validatedData['voice'];

        $this->dispatcher->dispatch(new SynthesizeBatchJob($texts, $voice));

        return $this->responseFactory->json([
            'message' => 'Success',
        ], 200);
    }
}