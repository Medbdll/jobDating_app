<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\front\AuthController;
use App\Controllers\front\DashboardController as FrontDashboardController;
use App\Controllers\back\DashboardController;
use App\Controllers\back\StudentController;
use App\Controllers\back\AnnouncementController;
use App\Controllers\back\CampanyController;
use App\Controllers\back\ApplicationController;

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
$router->get('/student/dashboard', [StudentController::class, 'dashboard']);
$router->get('/student/dashboard/search', [StudentController::class, 'searchAnnouncements']);
$router->get('/student/dashboard/profile', [StudentController::class, 'profile']);
$router->post('/student/dashboard/profile', [StudentController::class, 'profile']);
$router->get('/student/dashboard/applications', [StudentController::class, 'applications']);
$router->get('/student/dashboard/announcement/{id}', [FrontDashboardController::class, 'showAnnouncement']);
$router->get('/student/dashboard/apply/{id}', [FrontDashboardController::class, 'apply']);
$router->post('/student/dashboard/apply/{id}/store', [FrontDashboardController::class, 'storeApplication']);
$router->get('/student/dashboard/applications', [FrontDashboardController::class, 'myApplications']);
$router->get('/announcements', function() {
    echo "Welcome to Student Announcements Page! <br>";
    echo '<a href="/logout">Logout</a>';
});

// Admin student management route
$router->get('/students', [StudentController::class, 'index']);

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
$router->post('/announcements/unarchive/{id}', [AnnouncementController::class, 'unarchive']);
$router->post('/announcements/deleteArchived/{id}', [AnnouncementController::class, 'deleteArchived']);

// Company routes
$router->get('/companies/create', [CampanyController::class, 'create']);
$router->post('/companies/store', [CampanyController::class, 'store']);
$router->get('/companies', [CampanyController::class, 'index']);
$router->get('/companies/edit/{id}', [CampanyController::class, 'edit']);
$router->post('/companies/update/{id}', [CampanyController::class, 'update']);
$router->post('/companies/delete/{id}', [CampanyController::class, 'delete']);

// Application routes
$router->get('/applications', [ApplicationController::class, 'index']);
$router->get('/applications/details/{id}', [ApplicationController::class, 'showDetails']);
$router->post('/applications/accept/{id}', [ApplicationController::class, 'accept']);
$router->post('/applications/reject/{id}', [ApplicationController::class, 'reject']);
$router->post('/applications/pending/{id}', [ApplicationController::class, 'pending']);

// 404 route
$router->get('/404', function(){
    echo "404 - Page not found";
});

$router->dispatch();