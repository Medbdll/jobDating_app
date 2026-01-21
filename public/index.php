<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\front\AuthController;
use App\Controllers\back\DashboardController;
use App\Controllers\back\AnnouncementController;

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
$router->get('/admin/dashboard', [DashboardController::class, 'index']);

// Announcement routes
$router->get('/announcements/create', [AnnouncementController::class, 'create']);
$router->post('/announcements/store', [AnnouncementController::class, 'store']);
$router->get('/announcements/edit/{id}', [AnnouncementController::class, 'edit']);
$router->post('/announcements/update/{id}', [AnnouncementController::class, 'update']);
$router->get('/announcements/show/{id}', [AnnouncementController::class, 'show']);
$router->post('/announcements/delete/{id}', [AnnouncementController::class, 'delete']);
$router->get('/announcements/archived', [AnnouncementController::class, 'archived']);
$router->post('/announcements/archive/{id}', [AnnouncementController::class, 'archive']);

// 404 route
$router->get('/404', function(){
    echo "404 - Page not found";
});

$router->dispatch();