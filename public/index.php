<?php
declare(strict_types=1);

session_start();

$config = require __DIR__ . '/../config/config.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Core\Database;

// Init DB
Database::init($config['db']);

$router = new Router($config);

require_once dirname(__DIR__) . '/app/Helpers/phone_helper.php';

// Public routes
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Protected routes
$router->get('/', 'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// Clients
$router->get('/clients', 'ClientController@index');
$router->get('/clients/create', 'ClientController@create');
$router->post('/clients', 'ClientController@store');
$router->get('/clients/{id}', 'ClientController@show');
$router->get('/clients/{id}/edit', 'ClientController@edit');
$router->post('/clients/{id}', 'ClientController@update');
$router->post('/clients/{id}/deactivate', 'ClientController@deactivate');
$router->get('/clients/search', 'ClientController@search'); // for job form

// Jobs
$router->get('/jobs', 'JobController@index');
$router->get('/jobs/create', 'JobController@create');
$router->post('/jobs', 'JobController@store');
$router->get('/jobs/{id}', 'JobController@show');
$router->get('/jobs/{id}/edit', 'JobController@edit');
$router->post('/jobs/{id}', 'JobController@update');
$router->post('/jobs/{id}/payments', 'JobController@storePayment');
$router->post('/jobs/{id}/time-entries', 'JobController@storeTimeEntry');
$router->post('/jobs/{id}/disposal-events', 'JobController@storeDisposalEvent');
$router->post('/jobs/{id}/expenses', 'JobController@storeExpense');

// Sales
$router->get('/sales', 'SaleController@index');
$router->get('/sales/create', 'SaleController@create');
$router->post('/sales', 'SaleController@store');
$router->get('/sales/{id}', 'SaleController@show');
$router->get('/sales/{id}/edit', 'SaleController@edit');
$router->post('/sales/{id}', 'SaleController@update');

// Users (admin only)
$router->get('/admin/users', 'UserController@index');
$router->get('/admin/users/create', 'UserController@create');
$router->post('/admin/users', 'UserController@store');
$router->get('/admin/users/{id}/edit', 'UserController@edit');
$router->post('/admin/users/{id}', 'UserController@update');

// Global search
$router->get('/search', 'SearchController@index');

$router->dispatch();