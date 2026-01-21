<?php

namespace App\Controllers\back;

use App\Controllers\front\AuthController as FrontAuthController;
use App\Core\Auth;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class AuthController extends FrontAuthController
{
    private $auth;
    private $user;
    protected $session;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->user = new User();
    }

    /**
     * Show admin login form
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if ($this->auth->isLoggedIn()) {
            if ($this->auth->isAdmin()) {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/announcements');
            }
            return;
        }

        $csrfToken = Security::generateCSRFToken();
        $this->render('back/auth/login.twig', [
            'csrf_token' => $csrfToken,
            'title' => 'Connexion Administrateur'
        ]);
    }

    /**
     * Process admin login
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/login');
            return;
        }

        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->session->flash('error', 'Token de sécurité invalide');
            $this->redirect('/admin/login');
            return;
        }

        // Check login attempts
        if (!Security::checkLoginAttempts($_POST['email'] ?? '')) {
            $this->session->flash('error', 'Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes.');
            $this->redirect('/admin/login');
            return;
        }

        // Validate input
        $validator = new Validator();
        $validation = $validator->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!$validation) {
            $errors = $validator->errors();
            $this->session->flash('error', 'Veuillez corriger les erreurs du formulaire');
            $this->session->flash('errors', $errors);
            $this->session->flash('old', $_POST);
            $this->redirect('/admin/login');
            return;
        }

        $email = Security::clean($_POST['email']);
        $password = $_POST['password'];

        // Attempt login with admin role
        $user = $this->auth->login($email, $password, 'admin');

        if (!$user) {
            Security::recordLoginAttempt($email, false);
            $this->session->flash('error', 'Email ou mot de passe incorrect');
            $this->redirect('/admin/login');
            return;
        }

        // Record successful login
        Security::recordLoginAttempt($email, true);

        $this->session->flash('success', 'Bienvenue ' . $user['name'] . ' !');
        $this->redirect('/admin/dashboard');
    }

    /**
     * Process admin logout
     */
    public function logout(): void
    {
        if ($this->auth->isLoggedIn()) {
            $this->auth->logout();
            $this->session->flash('success', 'Vous avez été déconnecté avec succès');
        }

        $this->redirect('/admin/login');
    }
}
