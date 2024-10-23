<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PararellService;
use Illuminate\Contracts\Routing\ResponseFactory;

class PararellController extends Controller
{
    private PararellService $service;
    private ResponseFactory $responseFactory;

    public function __construct(PararellService $service, ResponseFactory $responseFactory)
    {
        $this->service = $service;
        $this->responseFactory = $responseFactory;
    }

    public function paralell(Request $request)
    {
        $this->service->processTexts($request->get('texts'));
        return $this->responseFactory->json(['mensaje' => 'Procesando textos...']);
    }
}
