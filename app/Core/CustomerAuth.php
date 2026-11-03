<?php
namespace App\Core;

use App\Core\Database;
use PDO;

class CustomerAuth
{
    protected static $sessionKey = 'customer_user';

    /**
     * Check if a customer is authenticated
     */
    public static function check(): bool
    {
        session_init();
        return !empty($_SESSION[self::$sessionKey]['id']) 
            && ($_SESSION[self::$sessionKey]['role'] ?? '') === 'customer';
    }

    /**
     * Get the authenticated customer user data
     */
    public static function user(): ?array
    {
        session_init();
        return $_SESSION[self::$sessionKey] ?? null;
    }

    /**
     * Get the authenticated customer ID
     */
    public static function id(): ?int
    {
        $u = self::user();
        return isset($u['id']) ? (int)$u['id'] : null;
    }

    /**
     * Attempt customer login with email & password
     */
    public static function attempt(string $email, string $password): bool
    {
        session_init();
        $email = trim(strtolower($email));

        $db = Database::getConnection();
        if (!$db) return false;

        try {
            $stmt = $db->prepare("SELECT id, first_name, last_name, email, password_hash, phone, role, status, age_verified, total_orders, total_spent FROM users WHERE LOWER(email) = ? AND role = 'customer' LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (isset($user['status']) && $user['status'] !== 'active') {
                    return false;
                }

                if (password_verify($password, $user['password_hash'])) {
                    self::login($user);
                    return true;
                }
            }
        } catch (\Exception $e) {
            error_log("CustomerAuth::attempt error: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Log in the given user array into customer session
     */
    public static function login(array $user): void
    {
        session_init();
        if (!headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }

        $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        if (empty($fullName)) {
            $fullName = 'Customer';
        }

        $_SESSION[self::$sessionKey] = [
            'id'           => (int)$user['id'],
            'name'         => $fullName,
            'first_name'   => $user['first_name'] ?? '',
            'last_name'    => $user['last_name'] ?? '',
            'email'        => $user['email'] ?? '',
            'phone'        => $user['phone'] ?? '',
            'role'         => 'customer',
            'status'       => $user['status'] ?? 'active',
            'age_verified' => (int)($user['age_verified'] ?? 1),
            'logged_in_at' => time()
        ];
    }

    /**
     * Log out current customer
     */
    public static function logout(): void
    {
        session_init();
        unset($_SESSION[self::$sessionKey]);
    }

    /**
     * Refresh customer session from database
     */
    public static function refresh(): void
    {
        $id = self::id();
        if (!$id) return;

        $db = Database::getConnection();
        if (!$db) return;

        try {
            $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, role, status, age_verified FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && ($user['status'] ?? '') === 'active') {
                self::login($user);
            } else {
                self::logout();
            }
        } catch (\Exception $e) {
            error_log("CustomerAuth::refresh error: " . $e->getMessage());
        }
    }

    /**
     * Enforce customer authentication guard
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            set_flash('error', 'Please log in or register to access your account dashboard.');
            redirect('my-account');
        }
    }
}
