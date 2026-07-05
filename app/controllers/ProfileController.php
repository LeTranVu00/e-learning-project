<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController {
    private $db;
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    // Hiển thị trang hồ sơ
    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để xem hồ sơ.";
            header("Location: ?action=login");
            exit;
        }

        // Lấy thông tin user hiện tại từ database
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy thông tin người dùng.";
            header("Location: ?action=home");
            exit;
        }

        // Nạp view
        require_once __DIR__ . '/../../views/profile.php';
    }

    // Xử lý cập nhật hồ sơ
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user_id'];
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            if (empty($fullname)) {
                $_SESSION['error'] = "Họ và tên không được để trống!";
                header("Location: ?action=profile");
                exit;
            }

            if ($this->userModel->updateProfile($id, $fullname, $phone, $address, $bio)) {
                // Cập nhật lại session fullname
                $_SESSION['user_name'] = $fullname;
                $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại.";
            }

            header("Location: ?action=profile");
            exit;
        }
    }
}
?>
