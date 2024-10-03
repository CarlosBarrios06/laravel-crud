<?php

use App\Http\Controllers\API\PersonController;
use App\Http\Controllers\AUTH\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

//Rutas publicas
Route::post('auth/register', [AuthController::class, 'create']);
Route::post('auth/login', [AuthController::class, 'login']);

//Rutas Privadas
Route::middleware(['middleware' => 'auth:sanctum'])->group(function () {

    //Para Acceder a estas rutas, usa un header que sea: 'Authorization':'Bearer token_de_usuario'
    //Rutas de Person
    Route::get('obtener-personas', [PersonController::class, 'obtenerPersonas']);
    Route::post('crear-registro', [PersonController::class, 'crear']);
    Route::get('buscar-persona', [PersonController::class, 'buscar']);
    Route::put('actualizar-persona', [PersonController::class, 'actualizar']);
    Route::delete('eliminar-persona', [PersonController::class, 'eliminar']);
    Route::put('desactivar', [PersonController::class, 'desactivar']);
    Route::put('activar', [PersonController::class, 'activar']);
    Route::get('busqueda-avanzada', [PersonController::class, 'busquedaAvanzada']);
    Route::get('listar-personas', [PersonController::class, 'listar']);

    //logout
    Route::get('auth/logout', [AuthController::class, 'logout']);

});
