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

Route::get('/logout','Auth\LoginController@logout');


Route::group(['middleware' => 'auth'], function() {
    //Pagina pricipal
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    //

    Route::group(['prefix' => 'puestos'], function() {
        Route::get('/',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@index']);
	    Route::get('/edit/{id}',['middleware'=>'permissions:["Puestos"],["C"]', 'uses' => 'PuestosController@edit']);
	    Route::post('/update',['middleware'=>'permissions:["Puestos"],["C"]', 'uses' => 'PuestosController@update']);
        Route::get('/delete/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@delete']);
        Route::get('/ver_puesto/{id}',['middleware'=>'permissions:["Puestos"],["D"]', 'uses' => 'PuestosController@ver_puesto']);
        Route::get('/savesnapshot/{id}',['middleware'=>'permissions:["Puestos"],["R"]', 'uses' => 'PuestosController@savesnapshot']);

    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UsersController@index')->name('users.users.index');
        Route::get('/create','UsersController@create')->name('users.users.create');
        Route::get('/show/{users}','UsersController@show')->name('users.users.show');
        Route::get('/{users}/edit','UsersController@edit')->name('users.users.edit');
        Route::post('/', 'UsersController@store')->name('users.users.store');
        Route::post('users/{users}', 'UsersController@update')->name('users.users.update');
        Route::delete('/users/{users}','UsersController@destroy')->name('users.users.destroy');
    });

    Route::get('/bitacoras', 'BitacorasController@index')->name('bitacoras.bitacora.index');
    Route::post('/bitacoras/search', 'BitacorasController@search')->name('bitacoras.bitacora.search');


    Route::get('profiles',['middleware'=>'permissions:["Perfiles"],["R"]','uses'=>'PermissionsController@profiles']);
	Route::get('profiles/edit/{id}',['middleware'=>'permissions:["Perfiles"],["C"]','uses'=>'PermissionsController@profilesEdit']);
	Route::post('profiles/save',['middleware'=>'permissions:["Perfiles"],["W"]','uses'=>'PermissionsController@profilesSave']);
	Route::post('profiles/update',['middleware'=>'permissions:["Perfiles"],["C"]','uses'=>'PermissionsController@profilesSave']);
	Route::get('profiles/delete/{id}',['middleware'=>'permissions:["Perfiles"],["D"]','uses'=>'PermissionsController@profilesDelete']);

	Route::get('sections',['middleware'=>'permissions:["Secciones"],["R"]','uses'=>'PermissionsController@sections']);
	Route::get('sections/edit/{id}',['middleware'=>'permissions:["Secciones"],["C"]','uses' => 'PermissionsController@sectionsEdit']);
	Route::post('sections/save',['middleware'=>'permissions:["Secciones"],["W"]','uses' => 'PermissionsController@sectionsSave']);
	Route::post('sections/update',['middleware'=>'permissions:["Secciones"],["C"]','uses' => 'PermissionsController@sectionsSave']);
	Route::get('sections/delete/{id}',['middleware'=>'permissions:["Secciones"],["D"]','uses' => 'PermissionsController@sectionsDelete']);

	Route::get('profile-permissions',['middleware'=>'permissions:["Permisos"],["R"]','uses'=>'PermissionsController@profilePermissions']);
    Route::get('permissions/getProfiles',['middleware'=>'permissions:["Usuarios"],["W"]','uses'=>'PermissionsController@getProfiles']);

	/**/
	Route::post('addPermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions']);
	Route::post('removePermissions',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions']);
	Route::post('addPermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@addPermissions_user']);
	Route::post('removePermissions_user',['middleware'=>'permissions:["Permisos"],["W"]','uses'=>'PermissionsController@removePermissions_user']);
});

    Route::group(['prefix' => 'clientes'], function() {
        Route::get('/',['middleware'=>'permissions:["Empresas"],["R"]', 'uses' => 'CustomersController@index']);
        Route::get('create',['middleware'=>'permissions:["Empresas"],["W"]', 'uses' => 'CustomersController@create']);
        Route::post('save',['middleware'=>'permissions:["Empresas"],["W"]', 'uses' => 'CustomersController@save']);
        Route::get('edit/{id}',['middleware'=>'permissions:["Empresas"],["C"]', 'uses' => 'CustomersController@edit']);
        Route::post('update',['middleware'=>'permissions:["Empresas"],["C"]', 'uses' => 'CustomersController@update']);
        Route::get('delete/{id}',['middleware'=>'permissions:["Empresas"],["D"]', 'uses' => 'CustomersController@delete']);
});

