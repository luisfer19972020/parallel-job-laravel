<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PararellService;

class PararellController extends Controller
{
    private PararellService $service;

    public function __construct(PararellService $service)
    {
        $this->service = $service;
    }

    public function paralell(Request $request)
    {
        $this->service->processTexts($request->get('texts'));
        return response()->json(['mensaje' => 'Procesando textos...']);
    }
}
