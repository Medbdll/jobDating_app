<?php

namespace App\Controllers\front;

use App\Core\BaseController;
use App\Core\Auth;
use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;
use App\Models\Student;

class AuthController extends BaseController
{
    private $auth;
    private $user;
    protected $session;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new Auth();
        $this->user = new User();
        $this->session = Session::getInstance();
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if ($this->auth->isLoggedIn()) {
            header('Location: /announcements');
            exit;
        }

        $csrfToken = Security::generateCSRFToken();
        $this->render('login', [
            'csrf_token' => $csrfToken,
            'title' => 'Connexion Apprenant'
        ]);
    }

    /**
     * Process login
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->session->flash('error', 'Token de sécurité invalide');
            header('Location: /login');
            exit;
        }

        // Check login attempts
        if (!Security::checkLoginAttempts($_POST['email'] ?? '')) {
            $this->session->flash('error', 'Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes.');
            header('Location: /login');
            exit;
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
            header('Location: /login');
            exit;
        }

        $email = Security::clean($_POST['email']);
        $password = $_POST['password'];

        // Attempt login (allow both student and admin roles)
        $user = $this->auth->login($email, $password, null);

        if (!$user) {
            Security::recordLoginAttempt($email, false);
            $this->session->flash('error', 'Email ou mot de passe incorrect');
            header('Location: /login');
            exit;
        }

        // Record successful login
        Security::recordLoginAttempt($email, true);

        $this->session->flash('success', 'Bienvenue ' . $user['name'] . ' !');
        
        // Redirect based on user role
        if ($user['role'] === 'admin') {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /announcements');
        }
        exit;
    }

    /**
     * Process logout
     */
    public function logout()
    {
        if ($this->auth->isLoggedIn()) {
            $this->auth->logout();
            $this->session->flash('success', 'Vous avez été déconnecté avec succès');
        }

        header('Location: /login');
        exit;
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        // Redirect if already logged in
        if ($this->auth->isLoggedIn()) {
            if ($this->auth->isAdmin()) {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /announcements');
            }
            exit;
        }

        $csrfToken = Security::generateCSRFToken();
        $this->render('register', [
            'csrf_token' => $csrfToken,
            'title' => 'Inscription'
        ]);
    }

    /**
     * Process registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->session->flash('error', 'Token de sécurité invalide');
            header('Location: /register');
            exit;
        }

        // Get role from form
        $role = $_POST['role'] ?? 'student';

        // Validate input based on role
        $validator = new Validator();
        
        if ($role === 'admin') {
            $validation = $validator->validate($_POST, [
                'name' => 'required|min:2',
                'email' => 'required|email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required'
            ]);
        } else {
            $validation = $validator->validate($_POST, [
                'name' => 'required|min:2',
                'email' => 'required|email',
                'password' => 'required|min:8',
                'password_confirmation' => 'required',
                'promotion' => 'required',
                'specialization' => 'required'
            ]);
        }

        if (!$validation) {
            $errors = $validator->errors();
            $this->session->flash('error', 'Veuillez corriger les erreurs du formulaire');
            $this->session->flash('errors', $errors);
            $this->session->flash('old', $_POST);
            header('Location: /register');
            exit;
        }

        // Check password confirmation
        if ($_POST['password'] !== $_POST['password_confirmation']) {
            $this->session->flash('error', 'Les mots de passe ne correspondent pas');
            $this->session->flash('old', $_POST);
            header('Location: /register');
            exit;
        }

        // Check if email already exists
        if ($this->user->findByEmail(Security::clean($_POST['email']))) {
            $this->session->flash('error', 'Cet email est déjà utilisé');
            $this->session->flash('old', $_POST);
            header('Location: /register');
            exit;
        }

        // Create user based on role
        try {
            // Debug: Log form data
            error_log("DEBUG: Form data received: " . print_r($_POST, true));
            
            $userData = [
                'name' => Security::clean($_POST['name']),
                'email' => Security::clean($_POST['email']),
                'password' => $_POST['password'],
                'role' => $role
            ];

            error_log("DEBUG: User data prepared: " . print_r($userData, true));

            if ($role === 'admin') {
                // Create admin user directly
                error_log("DEBUG: Creating admin user");
                $userId = $this->user->createAdmin($userData);

                if ($userId) {
                    // Auto-login after registration
                    $user = $this->auth->login($userData['email'], $_POST['password'], 'admin');
                    
                    $this->session->flash('success', 'Compte administrateur créé avec succès ! Bienvenue ' . $userData['name']);
                    header('Location: /admin/dashboard');
                    exit;
                }
            } else {
                // Create student with additional data
                $studentData = [
                    'promotion' => Security::clean($_POST['promotion']),
                    'specialization' => Security::clean($_POST['specialization'])
                ];

                error_log("DEBUG: Creating student with data: " . print_r($studentData, true));

                $student = new Student();
                $userId = $student->createWithUser($userData, $studentData);

                if ($userId) {
                    // Auto-login after registration
                    $user = $this->auth->login($userData['email'], $_POST['password'], 'student');
                    
                    $this->session->flash('success', 'Inscription réussie ! Bienvenue ' . $userData['name']);
                    header('Location: /announcements');
                    exit;
                }
            }

        } catch (\Exception $e) {
            error_log("DEBUG: Exception in registration: " . $e->getMessage());
            $this->session->flash('error', 'Une erreur est survenue lors de l\'inscription: ' . $e->getMessage());
            $this->session->flash('old', $_POST);
            header('Location: /register');
            exit;
        }
    }
}
