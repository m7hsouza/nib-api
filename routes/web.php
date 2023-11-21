<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/', function () use ($router) {
  return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
  $router->post('login', 'AuthController@login');
  $router->post('logout', 'AuthController@logout');
  $router->post('refresh', 'AuthController@refresh');
  $router->get('user-profile', 'AuthController@me');

  $router->get('/articles', 'ArticlesController@index');
  $router->group(['prefix' => 'article'], function () use ($router) {
    $router->get('/highlights', 'ArticlesController@highlights');
    $router->get('/{id}', 'ArticlesController@show');
    $router->post('/', 'ArticlesController@store');
    $router->put('/{id}', 'ArticlesController@update');
    $router->delete('/{id}', 'ArticlesController@delete');
  });

  $router->get('/users', 'UsersController@index');
  $router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('/me', 'UsersController@me');
    $router->get('/{id}', 'UsersController@show');
    $router->post('/', 'UsersController@store');
    $router->put('/{id}', 'UsersController@update');
    $router->delete('/{id}', 'UsersController@delete');
  });
});
