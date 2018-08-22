<?php

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

$router->group(['prefix' => 'api'], function() use ($router) {

    $router->get('projects', ['uses' => 'StyleguideController@index']);

    $router->get('projects/{id}', ['uses' => 'StyleguideController@show']);

    $router->post('projects', ['uses' => 'StyleguideController@create']);

    $router->delete('projects/{id}', ['uses' => 'StyleguideController@delete']);

    $router->put('projects/{id}', ['uses' => 'StyleguideController@update']);

    $router->get('projects/{id}/export', ['uses' => 'StyleguideExportController@export']);

});

