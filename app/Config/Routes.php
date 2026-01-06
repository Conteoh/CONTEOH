<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


//BACKEND PORTAL
$routes->group(BACKEND_PORTAL, ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'Backend_portal_general::index');
});

$routes->get('/', 'Home::index');
