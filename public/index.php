<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\front\AuthController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = Router::getRouter();

// Root route for testing
$router->get('/', function() {
    echo "Welcome to Job Dating! <br>";
    echo '<a href="/login">Login</a> | <a href="/register">Register</a>';
});

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

// Student routes
$router->get('/announcements', function() {
    echo "Welcome to Student Announcements Page! <br>";
    echo '<a href="/logout">Logout</a>';
});

// Admin routes
$router->get('/admin/dashboard', function() {
    echo "Welcome to Admin Dashboard! <br>";
    echo '<a href="/logout">Logout</a>';
});

// 404 route
$router->get('/404', function(){
    echo "404 - Page not found";
});

$router->dispatch();