<?php

use App\Http\Controllers\PararellController;
use App\Http\Services\PararellService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;


beforeEach(function () {
    $this->jsonResponseMock = Mockery::mock(JsonResponse::class);
    $this->pararellServiceMock = Mockery::mock(PararellService::class);
    $this->responseFactoryMock = Mockery::mock(ResponseFactory::class);
    $this->controller = new PararellController($this->pararellServiceMock, $this->responseFactoryMock);
});

afterAll(function () {
    Mockery::close();
});

test('procesa textos y devuelve respuesta JSON', function () {
    //Given - Teniendo un request con 2 textos
    $texts = ['texto1', 'texto2'];

    //Se dan comportamiento a los mocks
    $response = ['mensaje' => 'Procesando textos...'];
    $request = Request::create('/paralell', 'POST', ['texts' => $texts]);

    $this->pararellServiceMock
        ->shouldReceive('processTexts')
        ->once()
        ->with($texts);


    $this->responseFactoryMock
        ->shouldReceive('json')
        ->once()
        ->with($response)
        ->andReturn($this->jsonResponseMock);


    //When - Cuando se hace una petcion al controllador y su metodo paralell
    $response = $this->controller->paralell($request);

    //Then - Entonces nos aseguramos que la respuesta del controlador sea la esperada
    $this->assertEquals($this->jsonResponseMock, $response, "The response from controller is not the expected");

});
