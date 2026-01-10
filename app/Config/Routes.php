<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


//BACKEND PORTAL (Logged In)
$routes->group(BACKEND_PORTAL, ['filter' => 'backend_auth'], ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Backend_portal_general::index');
    $routes->get('dashboard', 'Backend_portal_general::index');
    $routes->get('profile', 'Backend_portal_general::profile');
});

//BACKEND PORTAL (Unlogged In)
$routes->group(BACKEND_PORTAL, ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('login', 'Backend_portal_auth::login');
    $routes->get('logout', 'Backend_portal_auth::logout');
    $routes->get('forget_password', 'Backend_portal_auth::forget_password');
    $routes->get('reset_password/(:any)', 'Backend_portal_auth::reset_password/$1');
});

//BACKEND API 
$routes->group(BACKEND_API, function ($routes) {
    $routes->post('general/login_submit', 'Backend_api_general::login_submit');
    $routes->post('general/sent_reset_password_link', 'Backend_api_general::sent_reset_password_link');
    $routes->post('general/reset_password', 'Backend_api_general::reset_password');
});

$routes->get('/', 'Home::index');
