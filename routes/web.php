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

$router->group(['middleware'=>'cors'],function($router){



$router->get('/stuffs','StuffController@index');
// $router->post('/login','UserController@login');
// $router->get('/logout','UserController@logout');


$router->group(['prefix' => 'Inbound-stuff/','middleware' => 'auth'], function() use ($router) {
    $router->get ('/data','InboundStuffController@index');
    $router->post('store','InboundStuffController@store');
    $router->get('/trash','InboundStuffController@trash');
    $router->get('{id}','InboundStuffController@show');
    $router->patch('/update/{id}','InboundStuffController@update');
    $router->delete('/{id}','InboundStuffController@destroy');
    $router->get('/restore/{id}','InboundStuffController@restore');
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

$router->group(['prefix' => 'Stuff-Stock/'], function() use ($router) {
    $router->get ('/data','StuffStockController@index');
    $router->post('add-stock/{id}', 'StuffStockController@addStock');
    $router->post('sub-stock/{id}', 'StuffStockController@subStock');


});

$router->group(['prefix' => 'lendings'], function() use ($router) {
    $router->get ('/data','lendingController@index');
    $router->post('/store','lendingController@store');
    $router->get('/restore/{id}','lendingController@restore');
    $router->get('/show/{id}','lendingController@show');
    $router->patch('/update/{id}','lendingController@update');
    $router->delete('/{id}','lendingController@destroy');



});
$router->group(['prefix' => 'restoration'], function() use ($router) {
    $router->get ('/data','restorationController@index');
    $router->post('/store/{lending_id}','restorationController@store');
    $router->post('/delete/{lending_id}','restorationController@store');
    $router->patch('/update/{id}','restorationController@update');

});
$router->post('/login','AuthController@login');
$router->get('/logout','AuthController@logout');
$router->get('/profile','AuthController@me');


$router->group(['prefix' => 'stuff/'], function() use ($router) {
    $router->get ('/data','stuffController@index');
    $router->post('/store','stuffController@store');
    $router->get('/trash','stuffController@trash');
    $router->get('{id}','stuffController@show');
    $router->patch('/update/{id}','stuffController@update');
    $router->delete('/delete/{id}','stuffController@destroy');
    $router->get('/restore/{id}','stuffController@restore');
    $router->delete('/permanent/{id}','stuffController@deletePermanen');

});


});
