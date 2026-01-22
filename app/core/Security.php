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

    // Alias pour verifyCSRFToken
    public static function validateCSRFToken($token)
    {
        return self::verifyCSRFToken($token);
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

    // Vérifie les tentatives de connexion
    public static function checkLoginAttempts($email, $maxAttempts = 5, $timeWindow = 300)
    {
        // Temporarily disable login attempt limiting
        return true;
        
        $db = \App\Core\Database::getInstance()->getConnection();
        
        $stmt = $db->prepare(
            "SELECT COUNT(*) as attempts FROM login_attempts 
             WHERE email = :email AND attempted_at > DATE_SUB(NOW(), INTERVAL :timeWindow SECOND) AND success = FALSE"
        );
        $stmt->execute(['email' => $email, 'timeWindow' => $timeWindow]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['attempts'] >= $maxAttempts;
    }

    // Enregistre une tentative de connexion
    public static function recordLoginAttempt($email, $success)
    {
        $db = \App\Core\Database::getInstance()->getConnection();
        
        $stmt = $db->prepare(
            "INSERT INTO login_attempts (email, ip_address, success) 
             VALUES (:email, :ip_address, :success)"
        );
        
        return $stmt->execute([
            'email' => $email,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'success' => $success ? 1 : 0
        ]);
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload($field, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], $maxSize = 5242880)
    {
        $errors = [];
        
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error';
            return $errors;
        }

        $file = $_FILES[$field];
        
        // Check file size
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }

        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'File type not allowed';
        }

        // Check if it's actually an image
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                $errors[] = 'Invalid image file';
            }
        }

        return $errors;
    }

    /**
     * Generate secure filename
     */
    public static function generateSecureFilename($originalName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $basename));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug . '-' . uniqid() . '.' . $extension;
    }
}
