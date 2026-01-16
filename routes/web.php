<?php

$router = app('router');

$router->get('/', 'App\Controllers\HomeController@index')->name('home');

$router->get('/login', 'App\Controllers\AuthController@login')->name('login');
$router->post('/login', 'App\Controllers\AuthController@postLogin');
$router->get('/register', 'App\Controllers\AuthController@register')->name('register');
$router->post('/register', 'App\Controllers\AuthController@postRegister');
$router->get('/logout', 'App\Controllers\AuthController@logout')->name('logout');

$router->group(['prefix' => 'api'], function ($router) {
    $router->get('/users', 'App\Controllers\Api\UserController@index');
    $router->post('/users', 'App\Controllers\Api\UserController@store');
    $router->get('/users/{id}', 'App\Controllers\Api\UserController@show');
    $router->put('/users/{id}', 'App\Controllers\Api\UserController@update');
    $router->delete('/users/{id}', 'App\Controllers\Api\UserController@destroy');
});
