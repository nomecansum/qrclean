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
    return response()->json(['message' => 'Unauthenticated.'], 403);
});

//Funciones para login y registro
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'APIAuthController@login');
    Route::post('signup', 'APIAuthController@signUp');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'APIAuthController@logout');
        Route::get('user', 'APIAuthController@user');

        //Listado de entidades
        Route::get('entities', 'APIController@entidades');

    });
});


//FUNCIONES DEL API
Route::group(['middleware' => 'auth:api'], function() {
    //LLamada de test
    Route::get('test', 'APIController@test');
    //Listado de entidades
    Route::get('entidades', 'APIController@entidades');
});

////INCIDENCIAS/////
Route::group(['prefix' => 'incidencias','middleware' => 'auth:api'], function() {
    //Listado de incidencias
    Route::post('list', 'APIController@get_incidents');
    //Crear incidencia
    Route::put('/', 'APIController@crear_incidencia');

});