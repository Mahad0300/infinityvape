<?php
/**
 * Global Helpers for Infinity Vape Shop MVC Framework
 */

if (!function_exists('env')) {
    function env($key, $default = null) {
        static $envVars = null;
        if ($envVars === null) {
            $envVars = [];
            $envPath = dirname(dirname(__DIR__)) . '/.env';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || strpos($line, '#') === 0) continue;
                    if (strpos($line, '=') !== false) {
                        list($name, $value) = explode('=', $line, 2);
                        $name = trim($name);
                        $value = trim($value, " \t\n\r\0\x0B\"'");
                        $envVars[$name] = $value;
                    }
                }
            }
        }
        return $envVars[$key] ?? (getenv($key) ?: $default);
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        static $configs = [];
        $parts = explode('.', $key);
        $file = $parts[0];
        if (!isset($configs[$file])) {
            $path = dirname(dirname(__DIR__)) . '/config/' . $file . '.php';
            $configs[$file] = file_exists($path) ? require $path : [];
        }
        $val = $configs[$file];
        for ($i = 1; $i < count($parts); $i++) {
            if (!isset($val[$parts[$i]])) return $default;
            $val = $val[$parts[$i]];
        }
        return $val ?? $default;
    }
}

if (!function_exists('base_url')) {
    function base_url($path = '') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (!empty($host)) {
            $script = $_SERVER['SCRIPT_NAME'] ?? '';
            $dir = trim(dirname($script), '/\\');
            $dir = str_replace(['public'], '', $dir);
            $dir = trim($dir, '/\\');
            $baseUrl = $protocol . $host . ($dir ? '/' . $dir : '');
        } else {
            $baseUrl = config('app.url', 'http://localhost/vapefinalwebsite');
        }
        $baseUrl = rtrim($baseUrl, '/');
        $path = ltrim($path, '/');
        return $path !== '' ? $baseUrl . '/' . $path : $baseUrl;
    }
}

if (!function_exists('asset')) {
    function asset($path = '') {
        if (!$path) return '';
        if (preg_match('#^(https?://|//|data:)#i', $path)) {
            return $path;
        }
        $path = ltrim($path, '/');
        return base_url($path);
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        return base_url($path);
    }
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        if (!preg_match('#^https?://#i', $url)) {
            $url = base_url($url);
        }
        header("Location: $url");
        exit;
    }
}

if (!function_exists('session_init')) {
    function session_init() {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        session_init();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token = null) {
        session_init();
        if ($token === null) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        $validToken = $_SESSION['_csrf_token'] ?? '';
        return !empty($validToken) && hash_equals($validToken, (string)$token);
    }
}

if (!function_exists('set_flash')) {
    function set_flash($key, $message) {
        session_init();
        $_SESSION['_flash'][$key] = $message;
    }
}

if (!function_exists('get_flash')) {
    function get_flash($key, $default = null) {
        session_init();
        if (isset($_SESSION['_flash'][$key])) {
            $msg = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $msg;
        }
        return $default;
    }
}

if (!function_exists('is_valid_image_upload')) {
    function is_valid_image_upload($fileTmp, $fileName, &$detectedExt = null) {
        if (empty($fileTmp) || !file_exists($fileTmp) || !is_uploaded_file($fileTmp)) {
            return false;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'avif'];
        $ext = strtolower(pathinfo((string)$fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            return false;
        }

        if ($ext === 'svg') {
            $content = file_get_contents($fileTmp, false, null, 0, 8192);
            if ($content === false || (stripos($content, '<svg') === false && stripos($content, '<?xml') === false)) {
                return false;
            }
            if (preg_match('#<script|onload|onerror|javascript:#i', $content)) {
                return false;
            }
            $detectedExt = 'svg';
            return true;
        }

        $info = @getimagesize($fileTmp);
        if ($info === false) {
            if ($ext === 'webp') {
                $fh = @fopen($fileTmp, 'rb');
                if ($fh) {
                    $header = fread($fh, 12);
                    fclose($fh);
                    if (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP') {
                        $detectedExt = 'webp';
                        return true;
                    }
                }
            }
            if ($ext === 'avif') {
                $fh = @fopen($fileTmp, 'rb');
                if ($fh) {
                    $header = fread($fh, 16);
                    fclose($fh);
                    if (substr($header, 4, 4) === 'ftyp' && (strpos($header, 'avif') !== false || strpos($header, 'avis') !== false)) {
                        $detectedExt = 'avif';
                        return true;
                    }
                }
            }
            return false;
        }

        $mime = strtolower($info['mime'] ?? '');
        $allowedMimes = [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/avif'
        ];

        if (!in_array($mime, $allowedMimes, true)) {
            return false;
        }

        $detectedExt = $ext;
        return true;
    }
}

if (!function_exists('blog_time_display')) {
    function blog_time_display($datetime) {
        if (empty($datetime)) return date('M d, Y');
        $time = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if (!$time) return date('M d, Y');

        $diff = time() - $time;
        if ($diff >= 0 && $diff < 60) {
            return 'Just now';
        } elseif ($diff >= 60 && $diff < 3600) {
            $mins = max(1, (int)floor($diff / 60));
            return $mins . ' mins ago';
        } elseif ($diff >= 3600 && $diff < 86400) {
            $hours = max(1, (int)floor($diff / 3600));
            return $hours . ' hours ago';
        } else {
            return date('M d, Y', $time);
        }
    }
}



