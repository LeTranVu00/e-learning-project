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
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/profile.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
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

            // --- XỬ LÝ UPLOAD AVATAR ---
            $avatarPath = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                
                if ($file['size'] > $maxSize) {
                    $_SESSION['error'] = "Kích thước ảnh tối đa là 2MB.";
                    header("Location: ?action=profile");
                    exit;
                }

                // Kiểm tra MIME type thực sự
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($mime, $allowedMimes)) {
                    $_SESSION['error'] = "Định dạng ảnh không hợp lệ (chỉ hỗ trợ JPG, PNG, GIF, WEBP).";
                    header("Location: ?action=profile");
                    exit;
                }

                // Tạo tên file độc nhất tránh trùng lặp
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (empty($extension)) {
                    // Lấy extension dựa trên mime type nếu file không có đuôi
                    $mimeToExt = [
                        'image/jpeg' => 'jpg', 'image/png' => 'png',
                        'image/gif' => 'gif', 'image/webp' => 'webp'
                    ];
                    $extension = $mimeToExt[$mime] ?? 'jpg';
                }
                
                $newFilename = uniqid('avatar_') . '_' . time() . '.' . $extension;
                
                $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $destination = $uploadDir . $newFilename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Dùng URL base để truy cập từ ngoài
                    // Nhưng để tương thích với thẻ img src=".../public/uploads...", ta chỉ lưu chuỗi tương đối
                    // Project root public path: public/uploads/avatars/...
                    // Hoặc vì trang đang ở public/index.php, link tương đối là uploads/avatars/
                    // Lưu đường dẫn chuẩn so với public/index.php
                    // Ở views, ảnh gọi ra thường dùng htmlspecialchars($avatar).
                    // Khi đăng nhập bằng google thì đường dẫn http.
                    // Nếu là up bằng hệ thống: url = 'uploads/avatars/tên.jpg'
                    // Sẽ xử lý logic hiển thị ảnh ở View sau, ở đây ta lưu đường dẫn: 'public/uploads/avatars/' hoặc '/e-learning-project/public/uploads/avatars/' tùy cấu hình.
                    // Để đơn giản và tương đồng với ảnh mẫu, ta lưu chuỗi bắt đầu từ public/:
                    $avatarPath = 'uploads/avatars/' . $newFilename;
                } else {
                    $_SESSION['error'] = "Có lỗi xảy ra khi lưu ảnh tải lên.";
                    header("Location: ?action=profile");
                    exit;
                }
            }

            if ($this->userModel->updateProfile($id, $fullname, $phone, $address, $bio, $avatarPath)) {
                // Cập nhật lại session fullname
                $_SESSION['user_name'] = $fullname;
                if ($avatarPath) {
                    $_SESSION['user_avatar'] = $avatarPath; 
                }
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
