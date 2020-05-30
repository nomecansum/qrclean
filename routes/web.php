<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();
////////////////////////// RUTAS PARA LA OPERACION DE USUARIOS SIN LOGIN ////////////////////////////////
//Route::view('/', 'scan');
//Route::post('/getsitio','HomeController@getsitio');
// Route::get('/setqr/{sitio}','HomeController@setqr');
// Route::get('/getqr/{sitio}','HomeController@getqr');
Route::get('/puesto/{puesto}','HomeController@getpuesto');
Route::get('/puesto/estado/{puesto}/{estado}','HomeController@estado_puesto');

/////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('/logout','Auth\LoginController@logout');

//Route::view('/scan2', 'scan2');


Route::group(['middleware' => 'auth'], function() {
    //Pagina pricipal
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::view('/scan', 'scan');
    //

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UsersController@index')->name('users.users.index');
        Route::get('/create','UsersController@create')->name('users.users.create');
        Route::get('/show/{users}','UsersController@show')->name('users.users.show');
        Route::get('/{users}/edit','UsersController@edit')->name('users.users.edit');
        Route::post('/', 'UsersController@store')->name('users.users.store');
        Route::post('users/{users}', 'UsersController@update')->name('users.users.update');
        Route::delete('/users/{users}','UsersController@destroy')->name('users.users.destroy');
    });

    Route::group(['prefix' => 'filters'], function () {
        Route::post('/loadedificios', 'CombosController@loadedificios');
        Route::post('/loadplantas', 'CombosController@loadplantas');
        Route::post('/loadpuestos', 'CombosController@loadpuestos');
    });

    Route::group(['prefix' => 'bitacoras'], function () {
        Route::get('/', 'BitacorasController@index')->name('bitacoras.bitacora.index');
        Route::post('/search', 'BitacorasController@search')->name('bitacoras.bitacora.search');
    });

    Route::group(['prefix' => 'profiles'], function () {
        Route::get('/',['middleware'=>'permissions:["Perfiles"],["R"]','uses'=>'PermissionsController@profiles']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Perfiles"],["C"]','uses'=>'PermissionsController@profilesEdit']);
        Route::post('/save',['middleware'=>'permissions:["Perfiles"],["W"]','uses'=>'PermissionsController@profilesSave']);
        Route::post('/update',['middleware'=>'permissions:["Perfiles"],["C"]','uses'=>'PermissionsController@profilesSave']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Perfiles"],["D"]','uses'=>'PermissionsController@profilesDelete']);
    });

    Route::group(['prefix' => 'sections'], function () {
        Route::get('/',['middleware'=>'permissions:["Secciones"],["R"]','uses'=>'PermissionsController@sections']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Secciones"],["C"]','uses' => 'PermissionsController@sectionsEdit']);
        Route::post('/save',['middleware'=>'permissions:["Secciones"],["W"]','uses' => 'PermissionsController@sectionsSave']);
        Route::post('/update',['middleware'=>'permissions:["Secciones"],["C"]','uses' => 'PermissionsController@sectionsSave']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Secciones"],["D"]','uses' => 'PermissionsController@sectionsDelete']);
    });

    Route::group(['prefix' => 'clientes'], function() {
        Route::get('/',['middleware'=>'permissions:["Clientes"],["R"]', 'uses' => 'CustomersController@index']);
        Route::get('create',['middleware'=>'permissions:["Clientes"],[""]', 'uses' => 'CustomersController@create']);
        Route::post('save',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@save']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Clientes"],["C"]', 'uses' => 'CustomersController@edit']);
        Route::post('update',['middleware'=>'permissions:["Clientes"],["C"]', 'uses' => 'CustomersController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Clientes"],["D"]', 'uses' => 'CustomersController@delete']);
        Route::get('gen_key',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@gen_key']);
    });

    Route::group(['prefix' => 'puestos'], function() {
        Route::get('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@index']);
        Route::post('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@search']);
	    Route::get('/edit/{id}',['middleware'=>'permissions:["Puestos"],["C"]', 'uses' => 'PuestosController@edit']);
	    Route::post('/update',['middleware'=>'permissions:["Puestos"],["C"]', 'uses' => 'PuestosController@update']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@delete']);
        Route::get('/ver_puesto/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@ver_puesto']);
        Route::get('/savesnapshot/{id}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@savesnapshot']);
        Route::post('/accion_estado',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@accion_estado']);
        Route::Post('/print_qr','PuestosController@print_qr');
        Route::get('/mapa','PuestosController@mapa');
    });

    Route::group(['prefix' => 'edificios'], function () {
        Route::get('/', 'EdificiosController@index')->name('edificios.edificios.index');
        Route::get('/create','EdificiosController@create')->name('edificios.edificios.create');
        Route::get('/show/{edificios}','EdificiosController@show')->name('edificios.edificios.show')->where('id', '[0-9]+');
        Route::get('/edit/{edificios}','EdificiosController@edit')->name('edificios.edificios.edit')->where('id', '[0-9]+');
        Route::post('/', 'EdificiosController@store')->name('edificios.edificios.store');
        Route::put('edificios/{edificios}', 'EdificiosController@update')->name('edificios.edificios.update')->where('id', '[0-9]+');
        Route::get('/delete/{edificios}','EdificiosController@destroy')->name('edificios.edificios.destroy')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'plantas'], function () {
        Route::get('/', 'PlantasController@index')->name('plantas.plantas.index');
        Route::get('/create','PlantasController@create')->name('plantas.plantas.create');
        Route::get('/show/{plantas}','PlantasController@show')->name('plantas.plantas.show')->where('id', '[0-9]+');
        Route::get('/edit/{plantas}','PlantasController@edit')->name('plantas.plantas.edit')->where('id', '[0-9]+');
        Route::post('/', 'PlantasController@store')->name('plantas.plantas.store');
        Route::put('plantas/{plantas}', 'PlantasController@update')->name('plantas.plantas.update')->where('id', '[0-9]+');
        Route::get('/delete/{plantas}','PlantasController@destroy')->name('plantas.plantas.destroy')->where('id', '[0-9]+');
    });

    Route::get('profile-permissions',['middleware'=>'permissions:["Permisos"],["R"]','uses'=>'PermissionsController@profilePermissions']);
    Route::get('permissions/getProfiles',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'PermissionsController@getProfiles']);
	Route::post('addPermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions']);
	Route::post('removePermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions']);
	Route::post('addPermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions_user']);
    Route::post('removePermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions_user']);

});







