<?php
namespace App\Core;

use App\Core\Database;

class AdminAuth
{
    protected static $sessionKey = 'admin_user';

    /**
     * Check if an admin is authenticated
     */
    public static function check(): bool
    {
        session_init();
        return !empty($_SESSION[self::$sessionKey]['id']) 
            && in_array($_SESSION[self::$sessionKey]['role'] ?? '', ['admin', 'superadmin']);
    }

    /**
     * Get the authenticated admin user data
     */
    public static function user(): ?array
    {
        session_init();
        return $_SESSION[self::$sessionKey] ?? null;
    }

    /**
     * Get the authenticated admin ID
     */
    public static function id(): ?int
    {
        $u = self::user();
        return isset($u['id']) ? (int)$u['id'] : null;
    }

    /**
     * Attempt admin login with email & password
     */
    public static function attempt(string $email, string $password): bool
    {
        session_init();
        $email = trim(strtolower($email));

        if (empty($email) || empty($password)) {
            return false;
        }

        $db = Database::getConnection();
        if ($db) {
            try {
                $stmt = $db->prepare("SELECT id, first_name, last_name, email, password_hash, role, status FROM users WHERE LOWER(email) = ? AND role IN ('admin', 'superadmin') LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user) {
                    // Check if account status is active
                    if (isset($user['status']) && $user['status'] !== 'active') {
                        return false;
                    }

                    // Verify password strictly against database password_hash
                    if (password_verify($password, $user['password_hash'])) {
                        self::login($user);
                        return true;
                    }
                }
            } catch (\Exception $e) {
                error_log("Admin authentication error: " . $e->getMessage());
            }
        }

        return false;
    }

    /**
     * Log in the given user array into admin session
     */
    public static function login(array $user): void
    {
        session_init();
        if (!headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = 'Administrator';
        }

        $_SESSION[self::$sessionKey] = [
            'id' => (int)($user['id'] ?? 0),
            'name' => $fullName,
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'email' => $user['email'] ?? '',
            'role' => $user['role'] ?? 'admin',
            'logged_in_at' => time()
        ];
    }

    /**
     * Log out current admin user
     */
    public static function logout(): void
    {
        session_init();
        unset($_SESSION[self::$sessionKey]);
    }

    /**
     * Enforce authentication guard for admin pages
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            set_flash('error', 'Please log in with admin credentials to access the portal.');
            redirect('admin/login');
        }
    }
}
