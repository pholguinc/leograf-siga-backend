<?php

use App\Http\Controllers\Api\SedesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*Endpoints de Sedes */

Route::post('/sedes', [SedesController::class, 'store']);
Route::get('/sedes', [SedesController::class,'index']);
Route::get('/sedes/{id}', [SedesController::class, 'show']);
Route::put('/sedes/{id}', [SedesController::class, 'update']);
Route::put('/sedes/{id}', [SedesController::class, 'delete']);


/*Endpoints de Sedes */