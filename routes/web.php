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
Route::post('/puesto/estado/{puesto}/{estado}','HomeController@estado_puesto');

Route::view('/prueba_mail','emails.mail_incidencia');

Route::group(['prefix' => 'MKD'], function () {
    Route::get('/plano/{planta}/{token}','MKDController@plano');
    Route::get('/datos_plano/{planta}/{token}','MKDController@datos_plano');
});   

/////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('/logout','Auth\LoginController@logout');

//Route::view('/scan2', 'scan2');


Route::group(['middleware' => 'auth'], function() {
    //Pagina pricipal
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::view('/scan', 'scan');
    Route::view('/lockscreen','lock');
    //

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UsersController@index')->name('users.index');
        Route::get('/create','UsersController@create')->name('users.users.create');
        Route::get('/show/{users}','UsersController@show')->name('users.users.show');
        Route::get('/{users}/edit','UsersController@edit')->name('users.users.edit');
        Route::get('/plantas/{id}','UsersController@plantas_usuario')->name('users.plantas');
        Route::post('/', 'UsersController@store')->name('users.users.store');
        Route::post('users/{users}', 'UsersController@update')->name('users.users.update');
        Route::delete('/users/{users}','UsersController@destroy')->name('users.users.destroy');

        Route::get('/addplanta/{usuario}/{planta}','UsersController@addplanta')->name('users.addplanta');
        Route::get('/delplanta/{usuario}/{planta}','UsersController@delplanta')->name('users.delplanta');

        Route::get('/setdefcamera/{id}','UsersController@setdefcamera');
    });

    Route::group(['prefix' => 'filters'], function () {
        Route::post('/loadedificios', 'CombosController@loadedificios');
        Route::post('/loadplantas', 'CombosController@loadplantas');
        Route::post('/loadpuestos', 'CombosController@loadpuestos');
    });

    Route::group(['prefix' => 'combos'], function () {
        Route::post('/limpiadores', 'CombosController@combo_limpiadores');
        Route::get('/plantas/{id_edificio}', 'CombosController@combo_plantas');
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
        Route::get('/',['middleware'=>'permissions:["Clientes"],["R"]', 'uses' => 'CustomersController@index'])->name('clientes.index');
        Route::get('create',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@create']);
        Route::post('save',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@save']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@edit']);
        Route::post('update',['middleware'=>'permissions:["Clientes"],["C"]', 'uses' => 'CustomersController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Clientes"],["D"]', 'uses' => 'CustomersController@delete']);
        Route::get('gen_key',['middleware'=>'permissions:["Clientes"],["W"]', 'uses' => 'CustomersController@gen_key']);
    });

    Route::group(['prefix' => 'puestos'], function() {
        Route::get('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@index'])->name('puestos.index');
        Route::post('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@search']);
	    Route::get('/edit/{id}',['middleware'=>'permissions:["Puestos"],["C"]', 'uses' => 'PuestosController@edit']);
	    Route::post('/update',['middleware'=>'permissions:["Puestos"],["C"]', 'uses' => 'PuestosController@update']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@delete']);
        Route::get('/ver_puesto/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@ver_puesto']);
        Route::get('/savesnapshot/{id}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@savesnapshot']);
        Route::post('/accion_estado',['middleware'=>'permissions:["Puestos"],["W"]', 'uses' => 'PuestosController@accion_estado']);
        Route::Post('/print_qr','PuestosController@print_qr');
        Route::get('/mapa','PuestosController@mapa');
        Route::post('/ronda_limpieza','PuestosController@ronda_limpieza');
        Route::get('/plano','PuestosController@plano');
        Route::post('/anonimo','PuestosController@cambiar_anonimo');
        Route::post('/mca_rerserva','PuestosController@cambiar_reserva');
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
        Route::get('/puestos/{id}','PlantasController@puestos')->name('plantas.puestos.index')->where('id', '[0-9]+');
        Route::post('/puestos','PlantasController@puestos_save')->name('plantas.puestos.save')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'rondas'], function () {
        Route::get('/view/{id}/{print}', 'LimpiezaController@view')->name('rondas.view');
        Route::post('/estado_puesto_ronda', 'LimpiezaController@estado_puesto')->name('rondas.estado_puesto_ronda');
        Route::get('/index/{f1?}/{f2?}', 'LimpiezaController@index')->name('rondas.index');
        Route::get('/completar_ronda/{tipo}/{id}/{empleado}', 'LimpiezaController@completar_ronda')->name('rondas.completar');
        Route::get('/detallelimp/{id}', 'LimpiezaController@view_limpia')->name('rondas.detalle_limpiador');
        Route::get('/scan', 'LimpiezaController@scan')->name('rondas.estado_puesto');
    });

    Route::group(['prefix' => 'reservas'], function () {
        Route::get('/',['middleware'=>'permissions:["Reservas"],["R"]','uses'=>'ReservasController@index']);
        Route::get('/create/{fecha}',['middleware'=>'permissions:["Reservas"],["C"]','uses'=>'ReservasController@create']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Reservas"],["C"]','uses' => 'ReservasController@edit']);
        Route::post('/save',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@save']);

        Route::post('/cancelar',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@delete']);
        Route::post('loadMonthSchedule',['middleware'=>'permissions:["Reservas"],["R"]', 'uses' => 'ReservasController@loadMonthSchedule']);

        Route::post('/comprobar',['middleware'=>'permissions:["Reservas"],["W"]','uses' => 'ReservasController@comprobar_puestos']);
    });

    Route::group(['prefix' => 'incidencias'], function () {
        Route::get('/tipos',['middleware'=>'permissions:["Tipos de incidencia"],["R"]', 'uses' => 'IncidenciasController@index_tipos'])->name('incidencias_tipos.index');
        Route::post('/tipos/save',['middleware'=>'permissions:["Tipos de incidencia"],["W"]', 'uses' => 'IncidenciasController@tipos_save']);
        Route::get('/tipos/edit/{id?}',['middleware'=>'permissions:["Tipos de incidencia"],["C"]', 'uses' => 'IncidenciasController@tipos_edit']);

        Route::get('/create/{puesto}',['middleware'=>'permissions:["Incidencias"],["C"]', 'uses' => 'IncidenciasController@nueva_incidencia'])->name('incidencias.nueva');
        Route::get('/edit/{id}',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@edit']);
        Route::post('/save',['middleware'=>'permissions:["Incidencias"],["C"]','uses' => 'IncidenciasController@save']);
        Route::post('/cerrar',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@cerrar']);
        Route::get('/form_cierre/{id}',['middleware'=>'permissions:["Incidencias"],["W"]','uses' => 'IncidenciasController@form_cierre']);
        Route::get('/edit/{id}',['middleware'=>'permissions:["Incidencias"],["R"]','uses' => 'IncidenciasController@detalle_incidencia']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Incidencias"],["D"]','uses' => 'IncidenciasController@delete']);
        Route::get('/get_detalle/{id?}',['middleware'=>'permissions:["Incidencias"],["C"]', 'uses' => 'IncidenciasController@get_detalle_scan']);
        
        Route::get('/{f1?}/{f2?}',['middleware'=>'permissions:["Incidencias"],["R"]','uses'=>'IncidenciasController@index'])->name('incidencias.index');
        
    });

    Route::get('profile-permissions',['middleware'=>'permissions:["Permisos"],["R"]','uses'=>'PermissionsController@profilePermissions']);
    Route::get('permissions/getProfiles',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'PermissionsController@getProfiles']);
	Route::post('addPermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions']);
	Route::post('removePermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions']);
	Route::post('addPermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions_user']);
    Route::post('removePermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions_user']);

    Route::get('/scan_usuario', 'HomeController@scan_usuario')->name('main_scan');
    Route::get('/scan_mantenimiento', 'HomeController@scan_mantenimiento')->name('mantenimiento_scan');

});







