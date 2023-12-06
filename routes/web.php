<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/', function () use ($router) {
  return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
  $router->post('/login', 'AuthController@login');
  $router->post('/logout', 'AuthController@logout');
  $router->post('/refresh', 'AuthController@refresh');

  $router->get('/articles', 'ArticlesController@index');
  $router->get('/recent-articles', 'ArticlesController@recentArticles');
  $router->group(['prefix' => 'article'], function () use ($router) {
    $router->get('/{article_id}', 'ArticlesController@show');
    $router->get('/{article_id}/image', ['as' => 'article.image', 'uses' => 'ArticlesController@image']);

    $router->post('/', 'ArticlesController@store');
    $router->put('/{article_id}', 'ArticlesController@update');
    $router->delete('/{article_id}', 'ArticlesController@delete');
  });

  $router->get('/videos', 'VideosController@index');
  $router->get('/recent-videos', 'ArticlesController@recentVideos');
  $router->group(['prefix' => 'video'], function () use ($router) {
    $router->get('/{video_id}', 'VideosController@show');
    $router->get('/{video_id}/file', ['as' => 'video.file', 'uses' => 'VideosController@file']);
    $router->get('/{video_id}/thumbnail', ['as' => 'video.thumbnail', 'uses' => 'VideosController@thumbnail']);
    $router->post('/', 'VideosController@store');
    $router->put('/{video_id}', 'VideosController@update');
    $router->delete('/{video_id}', 'VideosController@delete');
  });

  $router->get('/cards', 'CardsController@index');
  $router->group(['prefix' => 'card'], function () use ($router) {
    $router->get('/{card_id}', 'CardsController@show');
    $router->get('/{card_id}/image', ['as' => 'card.image', 'uses' => 'CardsController@image']);

    $router->post('/', 'CardsController@store');
    $router->put('/{card_id}', 'CardsController@update');
    $router->delete('/{card_id}', 'CardsController@delete');
  });

  $router->get('/users', 'UsersController@index');
  $router->group(['prefix' => 'user'], function () use ($router) {
    $router->get('/me', 'UsersController@me');
    $router->get('/{user_id}', 'UsersController@show');
    $router->get('/{user_id}/avatar', ['as' => 'user.avatar', 'uses' => 'UsersController@getAvatar']);

    $router->post('/', 'UsersController@store');
    $router->post('/me/update-avatar', 'UsersController@updateAvatar');

    $router->put('/me', 'UsersController@updateMyProfile');
    $router->put('/{user_id}', 'UsersController@update');

    $router->delete('/{user_id}', 'UsersController@delete');
  });

  $router->get('/schedules', 'SchedulesController@index');
  $router->group(['prefix' => 'schedule'], function () use ($router) {
    $router->get('/{schedule_id}', 'SchedulesController@show');
    $router->get('/{schedule_id}', 'SchedulesController@show');
    $router->post('/', 'SchedulesController@store');
    $router->put('/{schedule_id}', 'SchedulesController@update');
    $router->delete('/{schedule_id}', 'SchedulesController@delete');

    $router->post('/{schedule_id}/task', 'SchedulesController@createTask');
    $router->put('/task/{task_id}', 'SchedulesController@updateTask');
  });
});
