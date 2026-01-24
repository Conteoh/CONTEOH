<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$module_list = [
    'mail_template',
    'user',
    'role',
    'enquiry',
    'page',
    'expenditure_category',
    'expenditure',
    'book',
    'car_maintenance',
    'jogging'
];

//BACKEND PORTAL (Logged In)
$routes->group(BACKEND_PORTAL, ['filter' => 'backend_auth'], ['namespace' => 'App\Controllers'], function ($routes) use ($module_list) {
    foreach ($module_list as $module) {
        $routes->group($module, function ($routes) use ($module) {
            $routes->get('list', 'Backend_portal_' . $module . '::list');
            $routes->get('add', 'Backend_portal_' . $module . '::action/0');
            $routes->get('edit/(:num)', 'Backend_portal_' . $module . '::action/$1');
        });
    }

    $routes->get('/', 'Backend_portal_general::index');
    $routes->get('dashboard', 'Backend_portal_general::index');
    $routes->get('profile', 'Backend_portal_general::profile');
    $routes->get('setting', 'Backend_portal_general::setting');
});

//BACKEND PORTAL (Unlogged In)
$routes->group(BACKEND_PORTAL, ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('login', 'Backend_portal_auth::login');
    $routes->get('logout', 'Backend_portal_auth::logout');
    $routes->get('forget_password', 'Backend_portal_auth::forget_password');
    $routes->get('reset_password/(:any)', 'Backend_portal_auth::reset_password/$1');
});

//BACKEND API 
$routes->group(BACKEND_API, function ($routes) use ($module_list) {
    foreach ($module_list as $module) {
        $routes->group($module, function ($routes) use ($module) {
            $routes->get('list/(:num)/(:any)', 'Backend_api_' . $module . '::list/$1/$2');
            $routes->post('submit', 'Backend_api_' . $module . '::submit');
            $routes->delete('delete/(:num)', 'Backend_api_' . $module . '::delete/$1');
        });
    }
    
    // Expenditure specific routes
    $routes->get('expenditure/get_tag_suggestions/(:num)/(:any)', 'Backend_api_expenditure::get_tag_suggestions/$1/$2');
    
    // Car maintenance specific routes
    $routes->get('car_maintenance/get_tag_suggestions/(:num)/(:any)', 'Backend_api_car_maintenance::get_tag_suggestions/$1/$2');

    //General
    $routes->post('general/login_submit', 'Backend_api_general::login_submit');
    $routes->post('general/sent_reset_password_link', 'Backend_api_general::sent_reset_password_link');
    $routes->post('general/reset_password', 'Backend_api_general::reset_password');
    $routes->post('general/update_profile', 'Backend_api_general::update_profile');
    $routes->post('general/upload_image_now', 'Backend_api_general::upload_image_now');

    //Setting
    $routes->post('setting/batch_update', 'Backend_api_setting::batch_update');

    //Role
    $routes->post('role/load_role_permission', 'Backend_api_role::load_role_permission');
});

//SYSTEM
$routes->match(['cli', 'get'], 'gen/(:any)', 'Mvc_generator::$1');

$routes->get('/', 'Frontend_general_portal::index');
