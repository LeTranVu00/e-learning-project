<?php
// File: app/controllers/AuthController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Mailer.php';

class AuthController {
    
    private $client;

    public function __construct() {
        // Khởi tạo đối tượng Google Client
        $this->client = new Google\Client();
        
        // Đọc credentials từ biến môi trường (.env)
        $this->client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $this->client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        
        // Đường dẫn Google sẽ trả dữ liệu về
        $this->client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        
        // Yêu cầu Google cung cấp Email và Profile
        $this->client->addScope("email");
        $this->client->addScope("profile");
    }

    // ==========================================
    // PHẦN 1: CÁC HÀM HIỂN THỊ GIAO DIỆN CŨ
    // ==========================================

    public function showLogin() {
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/auth/login.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Xử lý đăng nhập thường bằng email + mật khẩu
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=login');
            exit();
        }

        require_once __DIR__ . '/../utils/Security.php';
        if (!Security::checkLoginRateLimit()) {
            $_SESSION['error'] = 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.';
            header('Location: ?action=login');
            exit();
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu!';
            header('Location: ?action=login');
            exit();
        }

        $db = (new Database())->getConnection();
        $userModel = new User($db);
        $user = $userModel->findByEmail($email);

        // Kiểm tra user tồn tại và mật khẩu khớp
        if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
            Security::resetLoginAttempts();
            $_SESSION['user_id']     = $user['id'];
            $_SESSION['user_name']   = $user['fullname'];
            $_SESSION['user_role']   = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? '';
            header('Location: ?action=home');
        } else {
            Security::recordFailedLogin();
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng!';
            header('Location: ?action=login');
        }
        exit();
    }

    public function showRegister() {
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/auth/register.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Xử lý đăng ký tài khoản mới bằng email + mật khẩu
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=register');
            exit();
        }

        $fullname        = trim($_POST['fullname'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Kiểm tra dữ liệu đầu vào
        if (empty($fullname) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin!';
            header('Location: ?action=register');
            exit();
        }
        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp!';
            header('Location: ?action=register');
            exit();
        }

        $db = (new Database())->getConnection();
        $userModel = new User($db);

        // Kiểm tra email đã tồn tại chưa
        $existingUser = $userModel->findByEmail($email);
        if ($existingUser) {
            // Phân biệt rõ: tài khoản Google SSO vs tài khoản thường
            if (!empty($existingUser['google_id'])) {
                $_SESSION['error'] = 'Email này đã đăng ký bằng Google! Vui lòng đăng nhập bằng nút “Tiếp tục với Google”.';
            } else {
                $_SESSION['error'] = 'Email này đã được sử dụng! Vui lòng đăng nhập hoặc dùng email khác.';
            }
            header('Location: ?action=register');
            exit();
        }

        // Hash mật khẩu an toàn rồi mới lưu
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $userId = $userModel->createUser($fullname, $email, $hashed);

        if ($userId) {
            // Gửi email chào mừng
            Mailer::sendWelcomeEmail($email, $fullname);
            $_SESSION['success'] = 'Tạo tài khoản thành công! Hãy đăng nhập.';
            header('Location: ?action=login');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
            header('Location: ?action=register');
        }
        exit();
    }

    // ==========================================
    // PHẦN: KHÔI PHỤC MẬT KHẨU
    // ==========================================

    public function showForgotPassword() {
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/auth/forgot_password.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=forgot_password');
            exit();
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $_SESSION['error'] = 'Vui lòng nhập email!';
            header('Location: ?action=forgot_password');
            exit();
        }

        $db = (new Database())->getConnection();
        $userModel = new User($db);
        $user = $userModel->findByEmail($email);

        if ($user) {
            // Tạo token ngẫu nhiên và thời hạn 1 giờ
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            if ($userModel->saveResetToken($email, $token, $expiry)) {
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
                $resetLink = $baseUrl . "?action=reset_password&token=" . $token;
                
                // Gửi email reset password thực tế
                if (Mailer::sendPasswordResetEmail($email, $resetLink)) {
                    $_SESSION['success'] = 'Yêu cầu khôi phục mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư (bao gồm cả thư rác).';
                } else {
                    $_SESSION['error'] = 'Lỗi gửi email, không thể gửi mã khôi phục.';
                }
            } else {
                $_SESSION['error'] = 'Lỗi hệ thống, không thể tạo mã khôi phục.';
            }
        } else {
            // Không nên báo email không tồn tại vì lý do bảo mật, nhưng để demo thì cho dễ nhìn:
            $_SESSION['error'] = 'Email chưa được đăng ký trong hệ thống!';
        }

        header('Location: ?action=forgot_password');
        exit();
    }

    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        $db = (new Database())->getConnection();
        $userModel = new User($db);
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $_SESSION['error'] = 'Đường dẫn khôi phục không hợp lệ hoặc đã hết hạn!';
            header('Location: ?action=login');
            exit();
        }

        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/auth/reset_password.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=login');
            exit();
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($password) || $password !== $password_confirm) {
            $_SESSION['error'] = 'Mật khẩu không khớp hoặc bị trống!';
            header('Location: ?action=reset_password&token=' . $token);
            exit();
        }

        $db = (new Database())->getConnection();
        $userModel = new User($db);
        $user = $userModel->findByResetToken($token);

        if ($user) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            if ($userModel->updatePassword($user['id'], $hashed)) {
                $_SESSION['success'] = 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay bây giờ.';
                header('Location: ?action=login');
                exit();
            } else {
                $_SESSION['error'] = 'Đã xảy ra lỗi khi cập nhật mật khẩu.';
            }
        } else {
            $_SESSION['error'] = 'Đường dẫn khôi phục không hợp lệ hoặc đã hết hạn!';
        }

        header('Location: ?action=login');
        exit();
    }

    // PHẦN 2: CÁC HÀM XỬ LÝ GOOGLE SSO MỚI
    // ==========================================

    public function googleLogin() {
        $_SESSION['google_intent'] = $_GET['intent'] ?? 'login';
        $authUrl = $this->client->createAuthUrl();
        header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
        exit();
    }

    public function googleCallback() {
        if (isset($_GET['code'])) {
            // Đổi mã code lấy Access Token
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            $this->client->setAccessToken($token['access_token']);

            // Lấy thông tin User từ Google
            $google_oauth = new Google\Service\Oauth2($this->client);
            $google_account_info = $google_oauth->userinfo->get();
            
            $email =  $google_account_info->email;
            $name =  $google_account_info->name;
            $google_id = $google_account_info->id;
            
            // 1. THÊM DÒNG NÀY ĐỂ LẤY LINK ẢNH AVATAR TỪ GOOGLE
            $avatar = $google_account_info->picture; 

            // Kết nối DB
            $db = (new Database())->getConnection();
            $userModel = new User($db);

            // Kiểm tra xem User này đã tồn tại chưa
            $user = $userModel->findByEmail($email);

            $intent = $_SESSION['google_intent'] ?? 'login';

            if (!$user) {
                if ($intent === 'register') {
                    // Cập nhật: Tự động tạo tài khoản mới nếu bấm "Tiếp tục với Google" bên trang Đăng ký
                    $userId = $userModel->createGoogleUser($name, $email, $google_id, $avatar);
                    if ($userId) {
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_role'] = 'student';
                        $_SESSION['user_avatar'] = $avatar;
                        header('Location: ?action=home');
                        exit();
                    } else {
                        $_SESSION['error'] = 'Lỗi tạo tài khoản từ Google.';
                        header('Location: ?action=register');
                        exit();
                    }
                } else {
                    // Gmail chưa có tài khoản → Yêu cầu đăng ký trước
                    $_SESSION['error'] = 'Tài khoản Google “' . htmlspecialchars($email) . '” chưa được đăng ký. Vui lòng đăng ký tài khoản trước!';
                    header('Location: ?action=register');
                    exit();
                }
            }

            // Lưu thông tin vào Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_role'] = $user['role'];
            
            // 2. THÊM DÒNG NÀY ĐỂ LƯU AVATAR VÀO SESSION
            $_SESSION['user_avatar'] = $avatar; 

            // Đăng nhập thành công -> Chuyển về trang chủ
            header('Location: ?action=home');
            exit();
        } else {
            // Có lỗi xảy ra -> Quay lại trang đăng nhập
            header('Location: ?action=login');
            exit();
        }
    }
    // Hàm Đăng xuất
    public function logout() {
        // Xóa sạch toàn bộ Session
        session_destroy();
        // Đá về trang chủ
        header('Location: ?action=home');
        exit();
    }
}
?>