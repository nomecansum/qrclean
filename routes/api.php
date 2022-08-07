<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/unauthenticated', function () {
    return response()->json(['result'=>'error','timestamp'=>Carbon\Carbon::now(),'message' => 'Unauthenticated.'], 403);
});

Route::post('/test_request', 'APIController@echo_test');
Route::get('/test_request', 'APIController@echo_test');

//Funciones para login y registro
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'APIAuthController@login');
    Route::post('signup', 'APIAuthController@signUp');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'APIAuthController@logout');
        Route::get('user', 'APIAuthController@user');
    });
});


//FUNCIONES GENERALES DEL API
Route::group(['middleware' => 'auth:api'], function() {
    //LLamada de test
    Route::get('test', ['middleware'=>'permissions:["API General"],["R"]','uses'=>'APIController@test']);
    //Listado de entidades
    Route::get('entidades', ['middleware'=>'permissions:["API General"],["R"]','uses'=>'APIController@entidades']);
    //Listado de entidades
    Route::post('process_test', ['middleware'=>'permissions:["API General"],["R"]','uses'=>'APIController@process_test']);

});

////INCIDENCIAS/////
Route::group(['prefix' => 'incidencias','middleware' => 'auth:api'], function() {
    //Listado de incidencias
    Route::post('list', ['middleware'=>'permissions:["API Incidencias"],["R"]','uses'=>'APIController@get_incidents']);
    //Crear incidencia
    Route::put('/', ['middleware'=>'permissions:["API Incidencias"],["R"]','uses'=>'APIController@crear_incidencia']);
    //Añadir accion
    Route::post('/add_accion', ['middleware'=>'permissions:["API Incidencias"],["R"]','uses'=>'APIController@add_accion']);
    //Cerrar incidencia
    Route::post('/cerrar', ['middleware'=>'permissions:["API Incidencias"],["R"]','uses'=>'APIController@cerrar_ticket']);
     //Cerrar incidencia
    Route::post('/reabrir', ['middleware'=>'permissions:["API Incidencias"],["R"]','uses'=>'APIController@reabrir_ticket']);
});


////SALAS/////
Route::group(['prefix' => 'salas','middleware' => 'auth:api'], function() {
    //Listado de incidencias
    Route::get('sincronizar_estructura_incidencias_empresa_desde_fecha/{fecha}/{cliente}', ['middleware'=>'permissions:["API Salas"],["R"]','uses'=>'APIController@solicitud_sincro_datos']);
    //Crear incidencia
    Route::put('/', ['middleware'=>'permissions:["API Salas"],["R"]','uses'=>'APIController@crear_incidencia']);
    //Añadir accion
    Route::post('/add_accion', ['middleware'=>'permissions:["API Salas"],["R"]','uses'=>'APIController@add_accion']);
    //Cerrar incidencia
    Route::post('/cerrar', ['middleware'=>'permissions:["API Salas"],["R"]','uses'=>'APIController@cerrar_ticket']);
     //Cerrar incidencia
    Route::post('/reabrir', ['middleware'=>'permissions:["API Salas"],["R"]','uses'=>'APIController@reabrir_ticket']);

});
