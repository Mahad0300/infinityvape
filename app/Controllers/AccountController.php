<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\CustomerAuth;
use App\Core\Database;
use App\Models\Customer;
use PDO;

class AccountController extends Controller
{
    /**
     * Render Customer Account Page (Dashboard if logged in, Login/Register if guest)
     */
    public function index()
    {
        if (CustomerAuth::check()) {
            $customerId = CustomerAuth::id();
            $customer = Customer::find($customerId);

            if (!$customer) {
                // In case user record was removed, logout cleanly
                CustomerAuth::logout();
                redirect('my-account');
            }

            $this->render('front.account.dashboard', [
                'page_title' => 'My Account Dashboard | Infinity Vape Shop',
                'page_js'    => 'account',
                'customer'   => $customer,
                'user'       => CustomerAuth::user()
            ]);
            return;
        }

        $this->render('front.account.index', [
            'page_title' => 'My Account | Login & Register | Infinity Vape Shop',
            'page_js'    => 'account',
            'success'    => get_flash('success'),
            'error'      => get_flash('error')
        ]);
    }

    /**
     * Handle Customer Login Request
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('my-account');
        }

        $input = $this->getRequestInput();

        $email    = trim($input['email'] ?? ($input['username'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $token    = $input['_token'] ?? '';

        $isJson = $this->isJsonRequest();

        // CSRF Verification
        if (!verify_csrf_token($token)) {
            return $this->respondError('Session expired. Please refresh the page and try again.', 403, $isJson);
        }

        if (empty($email) || empty($password)) {
            return $this->respondError('Please enter both email and password.', 422, $isJson);
        }

        if (CustomerAuth::attempt($email, $password)) {
            $user = CustomerAuth::user();
            $message = 'Welcome back, ' . htmlspecialchars($user['first_name'] ?: 'Customer') . '!';
            set_flash('success', $message);

            if ($isJson) {
                if (!headers_sent()) header('Content-Type: application/json');
                echo json_encode([
                    'success'  => true,
                    'message'  => $message,
                    'redirect' => url('my-account')
                ]);
                return;
            }

            redirect('my-account');
        }

        // Check if this account belongs to an administrator trying to login on the customer portal
        $db = Database::getConnection();
        if ($db) {
            try {
                $checkStmt = $db->prepare("SELECT id, role FROM users WHERE LOWER(email) = ? AND role IN ('admin', 'superadmin') LIMIT 1");
                $checkStmt->execute([$email]);
                $adminUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($adminUser) {
                    return $this->respondError('This account has administrator privileges. Please log in via the Admin Portal (admin/login).', 403, $isJson);
                }
            } catch (\Exception $e) {}
        }

        return $this->respondError('Invalid email address or password. Please try again.', 401, $isJson);
    }

    /**
     * Handle Customer Registration Request
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('my-account');
        }

        $input = $this->getRequestInput();

        $firstName = trim($input['first_name'] ?? '');
        $lastName  = trim($input['last_name'] ?? '');
        $email     = trim(strtolower($input['email'] ?? ''));
        $password  = (string)($input['password'] ?? '');
        $phone     = trim($input['phone'] ?? '');
        $token     = $input['_token'] ?? '';

        $isJson = $this->isJsonRequest();

        // CSRF Verification
        if (!verify_csrf_token($token)) {
            return $this->respondError('Security validation failed. Please refresh and try again.', 403, $isJson);
        }

        // Field Validation
        if (empty($firstName)) {
            return $this->respondError('First name is required.', 422, $isJson);
        }
        if (empty($lastName)) {
            return $this->respondError('Last name is required.', 422, $isJson);
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondError('Please provide a valid email address.', 422, $isJson);
        }
        if (strlen($password) < 6) {
            return $this->respondError('Password must be at least 6 characters.', 422, $isJson);
        }

        $db = Database::getConnection();
        if (!$db) {
            return $this->respondError('Database service currently unavailable. Please try again later.', 500, $isJson);
        }

        try {
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE LOWER(email) = ? LIMIT 1");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return $this->respondError('This email is already registered. Please log in instead.', 409, $isJson);
            }

            // Create new customer account
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $insert = $db->prepare("INSERT INTO users (role, first_name, last_name, email, password_hash, phone, status, age_verified, created_at) VALUES ('customer', ?, ?, ?, ?, ?, 'active', 1, NOW())");
            $insert->execute([$firstName, $lastName, $email, $passwordHash, $phone]);
            $newId = (int)$db->lastInsertId();

            // Auto-login the new user
            $newUser = [
                'id'           => $newId,
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'email'        => $email,
                'phone'        => $phone,
                'role'         => 'customer',
                'status'       => 'active',
                'age_verified' => 1
            ];
            CustomerAuth::login($newUser);

            $message = 'Account created successfully! Welcome to New Vape, ' . htmlspecialchars($firstName) . '.';
            set_flash('success', $message);

            if ($isJson) {
                if (!headers_sent()) header('Content-Type: application/json');
                echo json_encode([
                    'success'  => true,
                    'message'  => $message,
                    'redirect' => url('my-account')
                ]);
                return;
            }

            redirect('my-account');
        } catch (\Exception $e) {
            error_log("AccountController::register error: " . $e->getMessage());
            return $this->respondError('Failed to create account. Please try again.', 500, $isJson);
        }
    }

    /**
     * Handle Customer Logout
     */
    public function logout()
    {
        CustomerAuth::logout();
        set_flash('success', 'You have been successfully logged out.');
        redirect('my-account');
    }

    /**
     * Update Customer Profile Details
     */
    public function updateProfile()
    {
        CustomerAuth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('my-account');
        }

        $input = $this->getRequestInput();
        $isJson = $this->isJsonRequest();

        $token = $input['_token'] ?? '';
        if (!verify_csrf_token($token)) {
            return $this->respondError('Session validation failed. Please refresh.', 403, $isJson);
        }

        $userId    = CustomerAuth::id();
        $firstName = trim($input['first_name'] ?? '');
        $lastName  = trim($input['last_name'] ?? '');
        $phone     = trim($input['phone'] ?? '');

        if (empty($firstName) || empty($lastName)) {
            return $this->respondError('First and last name are required.', 422, $isJson);
        }

        $db = Database::getConnection();
        if (!$db) {
            return $this->respondError('Database error.', 500, $isJson);
        }

        try {
            // Optional password update
            $currentPass = (string)($input['current_password'] ?? '');
            $newPass     = (string)($input['new_password'] ?? '');

            if (!empty($newPass)) {
                if (strlen($newPass) < 6) {
                    return $this->respondError('New password must be at least 6 characters.', 422, $isJson);
                }

                $chk = $db->prepare("SELECT password_hash FROM users WHERE id = ? LIMIT 1");
                $chk->execute([$userId]);
                $userRow = $chk->fetch(PDO::FETCH_ASSOC);

                if (!$userRow || !password_verify($currentPass, $userRow['password_hash'])) {
                    return $this->respondError('Current password is incorrect.', 422, $isJson);
                }

                $newHash = password_hash($newPass, PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, password_hash = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$firstName, $lastName, $phone, $newHash, $userId]);
            } else {
                $stmt = $db->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$firstName, $lastName, $phone, $userId]);
            }

            CustomerAuth::refresh();
            set_flash('success', 'Your profile details have been updated successfully.');

            if ($isJson) {
                if (!headers_sent()) header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'redirect' => url('my-account')
                ]);
                return;
            }

            redirect('my-account');
        } catch (\Exception $e) {
            error_log("AccountController::updateProfile error: " . $e->getMessage());
            return $this->respondError('Failed to update profile.', 500, $isJson);
        }
    }

    /**
     * Save / Update Shipping or Billing Address
     */
    public function saveAddress()
    {
        CustomerAuth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('my-account');
        }

        $input = $this->getRequestInput();
        $isJson = $this->isJsonRequest();

        $token = $input['_token'] ?? '';
        if (!verify_csrf_token($token)) {
            return $this->respondError('Security token expired.', 403, $isJson);
        }

        $userId      = CustomerAuth::id();
        $type        = in_array($input['address_type'] ?? '', ['shipping', 'billing']) ? $input['address_type'] : 'shipping';
        $firstName   = trim($input['first_name'] ?? '');
        $lastName    = trim($input['last_name'] ?? '');
        $street      = trim($input['street'] ?? ($input['street_address'] ?? ''));
        $apt         = trim($input['apartment'] ?? ($input['suite'] ?? ''));
        $city        = trim($input['city'] ?? '');
        $state       = trim($input['state'] ?? '');
        $zip         = trim($input['zip'] ?? ($input['postal_code'] ?? ''));
        $country     = trim($input['country'] ?? 'United States');
        $phone       = trim($input['phone'] ?? '');

        if (empty($street) || empty($city) || empty($state) || empty($zip)) {
            return $this->respondError('Street, city, state, and zip code are required.', 422, $isJson);
        }

        $db = Database::getConnection();
        if (!$db) {
            return $this->respondError('Database error.', 500, $isJson);
        }

        try {
            // Check if address already exists for this type
            $check = $db->prepare("SELECT id FROM user_addresses WHERE user_id = ? AND address_type = ? LIMIT 1");
            $check->execute([$userId, $type]);
            $existing = $check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $stmt = $db->prepare("UPDATE user_addresses SET first_name = ?, last_name = ?, street = ?, street_address = ?, apartment = ?, city = ?, state = ?, zip = ?, country = ?, phone = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$firstName, $lastName, $street, $street, $apt, $city, $state, $zip, $country, $phone, $existing['id']]);
            } else {
                $stmt = $db->prepare("INSERT INTO user_addresses (user_id, first_name, last_name, address_type, street, street_address, apartment, city, state, zip, country, phone, is_default, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
                $stmt->execute([$userId, $firstName, $lastName, $type, $street, $street, $apt, $city, $state, $zip, $country, $phone]);
            }

            set_flash('success', ucfirst($type) . ' address saved successfully.');

            if ($isJson) {
                if (!headers_sent()) header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => ucfirst($type) . ' address saved successfully.',
                    'redirect' => url('my-account')
                ]);
                return;
            }

            redirect('my-account');
        } catch (\Exception $e) {
            error_log("AccountController::saveAddress error: " . $e->getMessage());
            return $this->respondError('Failed to save address.', 500, $isJson);
        }
    }

    /**
     * Helpers for Request Parsing & Responses
     */
    protected function getRequestInput(): array
    {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                return array_merge($_POST, $json);
            }
        }
        return $_POST;
    }

    protected function isJsonRequest(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($accept, 'application/json') !== false || strpos($contentType, 'application/json') !== false;
    }

    protected function respondError(string $message, int $status = 400, bool $isJson = true)
    {
        if ($isJson) {
            http_response_code(200);
            if (!headers_sent()) header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $message]);
            return;
        }
        http_response_code($status);
        set_flash('error', $message);
        redirect('my-account');
    }
}
