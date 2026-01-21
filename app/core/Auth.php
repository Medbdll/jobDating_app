<?php

namespace App\Core;

use App\Models\User;
use App\Models\Student;

class Auth
{
    private $user;
    private $session;

    public function __construct()
    {
        $this->user = new User();
        $this->session = Session::getInstance();
    }

    /**
     * Authenticate user with email and password
     */
    public function login($email, $password, $role = null)
    {
        $user = $this->user->findByEmail($email);
        
        if (!$user || !$this->user->verifyPassword($password, $user['password'])) {
            return false;
        }

        if ($role && $user['role'] !== $role) {
            return false;
        }

        // Create secure session
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_email', $user['email']);
        $this->session->set('user_name', $user['name']);
        $this->session->set('user_role', $user['role']);
        $this->session->set('last_activity', time());
        $this->session->regenerate();

        return $user;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return $this->session->has('user_id');
    }

    /**
     * Check if current user is admin
     */
    public function isAdmin()
    {
        return $this->isLoggedIn() && $this->session->get('user_role') === 'admin';
    }

    /**
     * Check if current user is student
     */
    public function isStudent()
    {
        return $this->isLoggedIn() && $this->session->get('user_role') === 'student';
    }

    /**
     * Get current user data
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $this->session->get('user_id'),
            'email' => $this->session->get('user_email'),
            'name' => $this->session->get('user_name'),
            'role' => $this->session->get('user_role')
        ];
    }

    /**
     * Check session timeout (2 hours)
     */
    public function checkSessionTimeout()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $lastActivity = $this->session->get('last_activity');
        $timeout = 2 * 60 * 60; // 2 hours

        if (time() - $lastActivity > $timeout) {
            $this->logout();
            return false;
        }

        $this->session->set('last_activity', time());
        return true;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->session->destroy();
    }

    /**
     * Require authentication for specific role
     */
    public function requireAuth($role = null)
    {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if ($role && !$this->{'is' . ucfirst($role)}()) {
            header('Location: /login');
            exit;
        }

        $this->checkSessionTimeout();
    }

    /**
     * Get student details if current user is student
     */
    // public function getStudentDetails()
    // {
    //     if (!$this->isStudent()) {
    //         return null;
    //     }

    //     $student = new Student();
    //     return $student->findByUserId($this->session->get('user_id'));
    // }
}
