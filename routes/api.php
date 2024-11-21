<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PararellController;
use App\Http\Controllers\SynthesizeBatchController;
use App\Http\Controllers\SynthesizeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/user', function () {
    return 'Test';
});

Route::post('/paralell', [PararellController::class, 'paralell']);

Route::post('/batch/synthesize', SynthesizeBatchController::class)->name('synthesize.batch');
Route::post('/synthesize', SynthesizeController::class)->name('synthesize.single');
