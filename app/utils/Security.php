<?php
// File: app/utils/Security.php

class Security {
    /**
     * Initializes the CSRF token in the session if it doesn't exist
     */
    public static function initCSRF() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Validates the CSRF token for POST requests
     * @return bool
     */
    public static function verifyCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ignore CSRF for specific API callbacks if needed (e.g., VNPay return)
            if (isset($_GET['action']) && in_array($_GET['action'], ['vnpay_return'])) {
                return true;
            }

            $csrf_token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
                $_SESSION['error'] = 'Yêu cầu không hợp lệ (Lỗi bảo mật CSRF). Vui lòng thử lại.';
                
                // Redirect back to previous page or home
                $referer = $_SERVER['HTTP_REFERER'] ?? '?action=home';
                header("Location: $referer");
                exit();
            }
        }
        return true;
    }

    /**
     * Rate limit for login attempts
     * @param int $maxAttempts Maximum allowed attempts
     * @param int $lockoutTime Lockout time in seconds
     * @return bool True if allowed, false if locked out
     */
    public static function checkLoginRateLimit($maxAttempts = 5, $lockoutTime = 900) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['login_lockout']) && time() < $_SESSION['login_lockout']) {
            return false;
        }

        // If lockout time expired, reset attempts
        if (isset($_SESSION['login_lockout']) && time() >= $_SESSION['login_lockout']) {
            unset($_SESSION['login_lockout']);
            $_SESSION['login_attempts'] = 0;
        }

        return true;
    }

    /**
     * Record a failed login attempt
     * @param int $maxAttempts Maximum allowed attempts before lockout
     * @param int $lockoutTime Lockout time in seconds
     */
    public static function recordFailedLogin($maxAttempts = 5, $lockoutTime = 900) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        $_SESSION['login_attempts']++;

        if ($_SESSION['login_attempts'] >= $maxAttempts) {
            $_SESSION['login_lockout'] = time() + $lockoutTime;
        }
    }

    /**
     * Reset login attempts on successful login
     */
    public static function resetLoginAttempts() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['login_attempts']);
        unset($_SESSION['login_lockout']);
    }
}
?>
