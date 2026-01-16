<?php

/**
 * Web Routes
 * 
 * All web routes are registered here
 */

$router = app(\Framework\Routing\Router::class);

// Home route
$router->get('/', 'App\\Controllers\\HomeController@index')->name('home');

// Example resource route
// $router->resource('posts', 'App\\Controllers\\PostController');

// Example route group
// $router->group(['prefix' => 'api', 'middleware' => ['api']], function ($router) {
//     $router->get('/users', 'App\\Controllers\\Api\\UserController@index');
//     $router->post('/users', 'App\\Controllers\\Api\\UserController@store');
// });

// Example protected route
// $router->get('/dashboard', 'App\\Controllers\\DashboardController@index')
//     ->name('dashboard')
//     ->middleware('auth');
