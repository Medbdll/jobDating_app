<?php
namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Security;
use App\Models\User;

class AuthController extends BaseController {
    
    public function showLogin(){
        $this->view('login');
    }

    public static function verify(){
        $token = $_SESSION['csrf_token'];
        Security::verifyCSRFToken($token);
        var_dump($token);
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $token = $_POST['csrf_token'] ?? '';
            if(!Security::verifyCSRFToken($token)){
                die("CSRF token invalid!");
            }

            $email = Security::clean($_POST['email']);
            $password = $_POST['password'];

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if($user && Security::verifyPassword($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                header('Location: /users');
                exit();
            } else {
                echo "Email ou mot de pass incorec!";
            }
        }
    }

    public function logout(){
        session_destroy();
        header('Location: /login');
        exit();
    }

    public function testTwifg()
    {
            $this->render("login",["title"=>"welcome to login page"]);
}
}
