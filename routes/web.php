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

$router->post('users/login', 'UserController@login');

$router->group(['middleware' => 'authToken'], function () use ($router) {
  $router->get('user', 'UserController@userInfo');

  $router->post('car', 'CarController@createCar');

  $router->get('cars', 'CarController@carList');

  $router->post('searchCar', 'CarController@searchCar');

  $router->post('updateCar/{id}', 'CarController@updateCar');

  $router->post('uploadImage', 'ImageController@uploadImage');

  $router->post('deleteImage', 'ImageController@deleteImage');

  $router->get('imageList', 'ImageController@imageList');
});
