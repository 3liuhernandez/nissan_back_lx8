<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

# Rutas protegidas por token
$router->group(['middleware' => 'auth:api'], function() use ($router){

    # Datos del usuario conectado
    $router->get('/api/auth/whoami', '\App\Http\Controllers\AuthController@owner');

    # Edición de los datos personaels del usuario conectado
    $router->get('/api/edit', '\App\Http\Controllers\AuthController@edit');

});


# Registro de usuario
$router->post('/api/auth/register', '\App\Http\Controllers\AuthController@register');

# Login de usuario
$router->post('/api/auth/login', '\App\Http\Controllers\AuthController@login');
