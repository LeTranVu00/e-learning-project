<?php
// File: app/controllers/AuthController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    private $client;

    public function __construct() {
        // Khởi tạo đối tượng Google Client
        $this->client = new Google\Client();
        
        // ⚠️ BỎ CLIENT ID VÀ SECRET CỦA BRO VÀO ĐÂY ⚠️
        $this->client->setClientId('251298581512-qhta5ksrk1urn4lc0cai82kuj95lnsh1.apps.googleusercontent.com');
        $this->client->setClientSecret('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');
        
        // Đường dẫn Google sẽ trả dữ liệu về
        $this->client->setRedirectUri('http://localhost/e-learning-project/public/index.php?action=google_callback');
        
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

    public function showRegister() {
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/auth/register.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // ==========================================
    // PHẦN 2: CÁC HÀM XỬ LÝ GOOGLE SSO MỚI
    // ==========================================

    public function googleLogin() {
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

            if (!$user) {
                // Nếu chưa có, tự động đăng ký tài khoản mới
                $userModel->createGoogleUser($name, $email, $google_id);
                $user = $userModel->findByEmail($email);
            }

            // Lưu thông tin vào Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_role'] = $user['role'];
            
            // 2. THÊM DÒNG NÀY ĐỂ LƯU AVATAR VÀO SESSION
            $_SESSION['user_avatar'] = $avatar; 

            // Đăng nhập thành công -> Chuyển về trang chủ
            header('Location: index.php?action=home');
            exit();
        } else {
            // Có lỗi xảy ra -> Quay lại trang đăng nhập
            header('Location: index.php?action=login');
            exit();
        }
    }
    // Hàm Đăng xuất
    public function logout() {
        // Xóa sạch toàn bộ Session
        session_destroy();
        // Đá về trang chủ
        header('Location: index.php?action=home');
        exit();
    }
}
?>