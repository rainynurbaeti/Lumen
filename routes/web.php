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

$router->get('/stuffs','StuffController@index');
$router->post('/login','UserController@login');
$router->get('/logout','UserController@logout');


$router->group(['prefix' => 'Inbound-stuff/','middleware' => 'auth'], function() use ($router) {
    $router->get ('/data','InboundStuffController@index');
    $router->post('store','InboundStuffController@store');
    $router->get('/trash','StuffController@trash');
    $router->get('{id}','StuffController@show');
    $router->patch('/{id}','StuffController@update');
    $router->delete('/{id}','StuffController@destroy');
    $router->get('/restore/{id}','StuffController@restore');
    $router->delete('/{id}','InboundStuffController@destroy');
    $router->delete('/permanent/{id}','InboundStuffController@deletePermanent');





});
$router->get('/user','UserController@index');

$router->group(['prefix' => 'user'], function() use ($router) {
    $router->get ('/data','UserController@index');
    $router->post('/','UserController@store');
    $router->get('/trash','UserController@trash');
    $router->get('{id}','UserController@show');
    $router->patch('/{id}','UserController@update');
    $router->delete('/{id}','UserController@destroy');
    $router->get('/restore/{id}','UserController@restore');
    $router->delete('/permanent/{id}','UserController@deletePermanen');

});

$router->group(['prefix' => 'Stuff-Stock/','middleware' => 'auth'], function() use ($router) {
    $router->post('add-stock/{id}','StuffStockController@addStock');
});



