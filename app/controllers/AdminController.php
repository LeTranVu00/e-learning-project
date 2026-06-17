<?php
// File: app/controllers/AdminController.php

class AdminController {
    
    // Hàm khởi tạo: Chạy đầu tiên mỗi khi gọi AdminController
    public function __construct() {
        // Cửa ải bảo vệ: Phải đăng nhập VÀ role phải là 'admin'
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Cảnh báo: Bạn không có quyền truy cập khu vực Quản trị!";
            header('Location: ?action=home');
            exit();
        }
    }

    // Hiển thị trang Tổng quan (Dashboard)
    public function dashboard() {
        // Giao diện Admin thường có cấu trúc khác hẳn (Sidebar, Topbar)
        // Nên mình sẽ gọi thẳng một file giao diện riêng cho nó
        require_once __DIR__ . '/../../views/admin/dashboard.php';
    }
    // Hàm hiển thị Form thêm khóa học mới
    public function createCourse() {
        // Phải gọi kèm header/footer riêng của Admin (nếu có), 
        // hoặc ở đây mình nhúng thẳng vào file giao diện cho nhanh gọn.
        require_once __DIR__ . '/../../views/admin/create_course.php';
    }

    // Hàm xử lý lưu khóa học mới (Có upload ảnh)
    public function storeCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description']; // CKEditor tự động gom nội dung HTML vào đây
            $thumbnail_path = '';

            // XỬ LÝ UPLOAD ẢNH
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                // Thư mục lưu ảnh
                $upload_dir = __DIR__ . '/../../public/uploads/courses/';
                
                // Lấy đuôi file (ví dụ: .jpg, .png)
                $file_extension = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                
                // Đổi tên file thành chuỗi ngẫu nhiên + thời gian để không bao giờ bị trùng
                $new_file_name = 'course_' . time() . '_' . uniqid() . '.' . $file_extension;
                
                // Đường dẫn tuyệt đối để move file vào ổ cứng
                $target_file = $upload_dir . $new_file_name;

                // Thực hiện di chuyển file từ bộ nhớ tạm vào thư mục
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
                    // Đường dẫn tương đối để lưu vào Database và hiển thị ra Web
                    $thumbnail_path = '/public/uploads/courses/' . $new_file_name;
                }
            }

            // LƯU VÀO DATABASE
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Course.php';
            $db = (new Database())->getConnection();
            $courseModel = new Course($db);

            if ($courseModel->createCourse($title, $description, $thumbnail_path)) {
                // Dùng session success để kích hoạt SweetAlert2
                $_SESSION['success'] = "Tuyệt vời! Khóa học đã được tạo thành công.";
            } else {
                $_SESSION['error'] = "Đã xảy ra lỗi khi lưu vào cơ sở dữ liệu.";
            }

            // Quay lại trang Dashboard
            header('Location: ?action=admin_dashboard');
            exit();
        }
    }
}
?>