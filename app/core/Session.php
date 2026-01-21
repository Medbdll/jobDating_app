<?php

namespace App\Core;

class Session
{
    private static $instance = null;

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Récupère l'instance unique (Singleton)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Définit une valeur en session
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de la session
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Supprime une valeur de la session
     */
    public function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Détruit toute la session
     */
    public function destroy(): void
    {
        session_destroy();
        $_SESSION = [];
    }

    /**
     * Flash message (message temporaire)
     */
    public function flash(string $key, $value = null)
    {
        if ($value === null) {
            $message = $this->get("flash_{$key}");
            $this->delete("flash_{$key}");
            return $message;
        }

        $this->set("flash_{$key}", $value);
    }

    /**
     * Get all flash messages
     */
    public function getFlash(): array
    {
        $flashMessages = [];
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'flash_') === 0) {
                $flashKey = str_replace('flash_', '', $key);
                $flashMessages[$flashKey] = $value;
                $this->delete($key);
            }
        }
        return $flashMessages;
    }

    /**
     * Régénère l'ID de session (sécurité)
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Récupère toutes les données de session
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * Crée une session utilisateur sécurisée
     */
    public static function createUserSession($user)
    {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['expires_at'] = time() + (2 * 60 * 60); // 2 heures
        
        // Nettoyer les anciennes sessions expirées
        self::cleanupExpiredSessions();
    }

    /**
     * Détruit complètement la session
     */
    public static function destroySession()
    {
        // Invalider le token CSRF
        unset($_SESSION['csrf_token']);
        
        // Détruire la session
        session_destroy();
        $_SESSION = [];
        
        // Supprimer le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }

    /**
     * Vérifie si la session est expirée
     */
    public static function isExpired()
    {
        return isset($_SESSION['expires_at']) && time() > $_SESSION['expires_at'];
    }

    /**
     * Nettoie les sessions expirées
     */
    private static function cleanupExpiredSessions()
    {
        $db = \App\Core\Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
        $stmt->execute();
    }
}
