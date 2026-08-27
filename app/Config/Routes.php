<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::loginProcess');
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::registerProcess');
$routes->get('/logout', 'Auth::logout');

// Public Aduan routes
$routes->get('/aduan/buat', 'Aduan::buat');
$routes->post('/aduan/simpan', 'Aduan::simpan');
$routes->get('/aduan/lacak', 'Aduan::lacak');

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    
    $routes->get('/aduan', 'Aduan::index');

    $routes->get('/aduan/detail/(:num)', 'Aduan::detail/$1');
    $routes->post('/aduan/tanggapi/(:num)', 'Aduan::tanggapi/$1');
    
    // Admin routes
    $routes->post('/aduan/update-status/(:num)', 'Aduan::updateStatus/$1');
    $routes->get('/aduan/rekap', 'Aduan::rekap');

    // Kelola User (Admin only)
    $routes->get('/user', 'User::index');
    $routes->get('/user/download-template', 'User::downloadTemplate');
    $routes->post('/user/import', 'User::import');
    $routes->post('/user/store', 'User::store');
    $routes->post('/user/update/(:num)', 'User::update/$1');
    $routes->post('/user/wa-settings', 'User::saveWaSettings');
    $routes->delete('/user/delete/(:num)', 'User::delete/$1');
});
