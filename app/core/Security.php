<?php

namespace App\Core;

class Security
{
    // Génère un token CSRF
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
         
        $token = $_SESSION['csrf_token'];
        return $token;
    }

    // Vérifie le token CSRF
    public static function verifyCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // Nettoie une chaîne contre les attaques XSS
    public static function clean($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    

    // Hash un mot de passe
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Vérifie un mot de passe
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    // Vérifie si l'utilisateur est connecté
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Redirige si non connecté
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit();
        }
    }
}
