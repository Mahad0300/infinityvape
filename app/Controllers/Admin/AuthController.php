<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\AdminAuth;

class AuthController extends Controller
{
    /**
     * Show Admin Login Form or Handle POST Login
     */
    public function login()
    {
        // Handle POST submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf_token()) {
                set_flash('error', 'Security session expired. Please refresh and try again.');
                $this->redirect('admin/login');
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                set_flash('error', 'Please enter both your admin email and password.');
                $this->redirect('admin/login');
            }

            if (AdminAuth::attempt($email, $password)) {
                $this->redirect('admin/dashboard');
            } else {
                set_flash('error', 'Invalid admin credentials or account inactive.');
                $this->redirect('admin/login');
            }
        }

        // Handle GET: If already logged in, redirect to dashboard
        if (AdminAuth::check()) {
            $this->redirect('admin/dashboard');
        }

        $this->render('admin.auth.login', [
            'page_title' => 'Admin Login | Infinity Vape Shop',
            'error' => get_flash('error'),
            'success' => get_flash('success')
        ]);
    }

    /**
     * Handle Admin Logout
     */
    public function logout()
    {
        AdminAuth::logout();
        set_flash('success', 'You have been logged out securely.');
        $this->redirect('admin/login');
    }
}
