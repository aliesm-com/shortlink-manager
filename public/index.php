<?php

$config = require __DIR__ . '/../app/bootstrap.php';

use App\Router;
use App\Helpers;

$router = new Router($config);

// Admin auth
$router->get('/admin/login', 'AuthController@showLogin');
$router->post('/admin/login', 'AuthController@login');
$router->get('/admin/logout', 'AuthController@logout');

// Admin panel
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/links', 'AdminController@links');
$router->post('/admin/links', 'AdminController@createLink');
$router->post('/admin/links/{id}/toggle', 'AdminController@toggleLink');
$router->post('/admin/links/{id}/delete', 'AdminController@deleteLink');
$router->get('/admin/links/{id}/stats', 'AdminController@linkStats');

// Root — redirect to home_url from config
$router->get('/', function () use ($config) {
    $homeUrl = isset($config['home_url']) ? trim($config['home_url']) : '';

    if ($homeUrl !== '' && Helpers::isValidUrl($homeUrl)) {
        Helpers::redirect($homeUrl);
    }

    Helpers::redirect(Helpers::url($config, 'admin/login'));
});

// Public short link redirect
$router->get('/{slug}', 'RedirectController@show');

$router->dispatch();
