<?php

use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ModulosController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\SedesController;
use App\Http\Controllers\Api\SubmenuController;
use App\Http\Controllers\Api\UsuariosController;
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

Route::controller((SedesController::class))->prefix('sedes')->name('sede.')->group(function () {
    Route::get('/', 'index')->name('listar');
    Route::post('/','store')->name('crear');
    Route::get('/{id}', 'show')->name('ver');
    Route::put('/{id}', 'update')->name('editar');
    Route::delete('/{id}', 'delete')->name('eliminar');
});

/*Endpoints de Sedes */


/*Endpoints de Roles */

Route::controller((RolController::class))->prefix('rol')->name('rol.')->group(function () {
    Route::get('/', 'index')->name('listar');
    Route::post('/', 'store')->name('crear');
    Route::get('/{id}', 'show')->name('ver');
    Route::put('/{id}', 'update')->name('editar');
    Route::delete('/{id}', 'delete')->name('eliminar');
});


// /*Endpoints de Sedes */


// /*Endpoints de Modulos */

Route::controller((ModulosController::class))->prefix('modulo')->name('modulo.')->group(function () {
    Route::get('/', 'index')->name('listar');
    Route::post('/', 'store')->name('crear');
    Route::get('/{id}', 'show')->name('ver');
    Route::put('/{id}', 'update')->name('editar');
    Route::delete('/{id}', 'delete')->name('eliminar');

  
});

/*Endpoints de Modulos */


// /*Endpoints de Menus */

Route::controller((MenuController::class))->prefix('menus')->name('menus.')->group(function () {
    Route::get('/', 'index')->name('listar');
    Route::post('/', 'store')->name('crear');
    Route::get('/{id}', 'show')->name('ver');
    Route::put('/{id}', 'update')->name('editar');
    Route::delete('/{id}', 'delete')->name('eliminar');
});

/*Endpoints de Menus */


// /*Endpoints de SubMenus */

Route::controller((SubmenuController::class))->prefix('submenus')->name('submenus.')->group(function () {
    Route::get('/', 'index')->name('listar');
    Route::post('/', 'store')->name('crear');
    Route::get('/{id}', 'show')->name('ver');
    Route::put('/{id}', 'update')->name('editar');
    Route::delete('/{id}', 'delete')->name('eliminar');
});

/*Endpoints de SubMenus */


// /*Endpoints de Usuarios */

Route::controller((UsuariosController::class))->prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', 'index')->name('listar');
    Route::post('/', 'store')->name('crear');
    Route::get('/{id}', 'show')->name('ver');
    Route::put('/{id}', 'update')->name('editar');
    Route::delete('/{id}', 'delete')->name('eliminar');
});

/*Endpoints de Usuarios */

