<?php

require __DIR__ . '/../vendor/autoload.php';


use App\Core\Router;
use App\controllers\UserController;
use App\controllers\AuthController;
use App\core\Validator;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



$router = Router::getRouter();

//twig test
$router->get('/twig', callback: [AuthController::class, 'testTwifg']);



$router->get('/users/{id:[0-9]+}', [UserController::class, 'show']);


//all test
$router->get('/test-all', callback: [UserController::class, 'findByIdTest']);

//find
$router->get('/test-user/{id}', [UserController::class, 'findByIdTest']);



//creat test
$router->get('/hello', function () {
    echo "Router is working!";
});



//test delete
$router->get('/test-delete/{id}', [UserController::class, 'deleteTest']);


// update test
$router->get('/test-update/{id}', [UserController::class, 'updateTest']);





// Auth router
$router->get('/login', [AuthController::class, 'showLogin']);

$router->post('/login', [AuthController::class, 'login']);

$router->get('/logout', [AuthController::class, 'logout']);



$router->post('/test', [AuthController::class,'verify']);





//test validation
$router->get('/test-validation', function () {

    $data = [
        'email' => 'test@mail.com',
        'password' => '123456',
        'password_confirmation' => '123456'
    ];

    $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed'
    ];

    $validator = new Validator();

    if ($validator->validate($data, $rules)) {
        echo " Validation réussie";
    } else {
        echo " Erreurs de validation";
        echo "<pre>";
        print_r($validator->errors());
        echo "</pre>";
    }
});



//test session
use App\core\Session;

$router->get('/test-session-set', function () {
    $session = Session::getInstance();
    $session->set('user_id', 10);

    echo "Session valid";
});

$router->get('/test-session-get', function () {
    $session = Session::getInstance();

    echo "<pre>";
    print_r($session->get('user_id'));
    echo "</pre>";
});




//test delet 
$router->get('/test-session-delete', function () {
    $session = Session::getInstance();
    $session->delete('user_id');

    echo "User  session ";
});

// test SET flash
$router->get('/test-flash-set', function () {
    $session = Session::getInstance();
    $session->flash('success', 'Login successful!');

    echo "Flash message ";
});

















$router->get("/users/{id}", function($id){
    echo '100<br>';
    require __DIR__ . '/../views/user.php';
    echo '<br>test';
});


$router->get('/404', function(){
    echo "404 - Page not found";
});

$router->dispatch();
