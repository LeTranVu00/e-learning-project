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
        require_once __DIR__ . '/../config/Database.php';
        $db = (new Database())->getConnection();

        // Lấy số liệu thật từ DB
        $total_courses    = $db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        $total_users      = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
        $total_enrollments = $db->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();

        $stmt = $db->query("SELECT * FROM courses ORDER BY id DESC");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
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
            $price = isset($_POST['price']) ? intval($_POST['price']) : 0;
            $description = $_POST['description']; // CKEditor tự động gom nội dung HTML vào đây
            // BUG FIX: Khai báo trước khối if upload để không bị undefined khi không có file
            $benefits = $_POST['benefits'] ?? '';
            $requirements = $_POST['requirements'] ?? '';
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
                    // Đường dẫn tương đối từ public/index.php
                    $thumbnail_path = 'uploads/courses/' . $new_file_name;
                }
            }

            // LƯU VÀO DATABASE
            require_once __DIR__ . '/../config/Database.php';
            require_once __DIR__ . '/../models/Course.php';
            $db = (new Database())->getConnection();
            $courseModel = new Course($db);

            if ($courseModel->createCourse($title, $description, $thumbnail_path, $benefits, $requirements, $price)) {
                $_SESSION['success'] = "Tuyệt vời! Khóa học đã được tạo thành công.";
            } else {
                $_SESSION['error'] = "Xin lỗi! Có lỗi xảy ra khi tạo khóa học.";
            }

            // Quay lại trang Dashboard
            header('Location: ?action=admin_dashboard');
            exit();
        }
    }

    // 2. Hàm hiển thị trang Quản lý nội dung (Chương & Bài) của 1 khóa học cụ thể
    public function manageContent() {
        if (!isset($_GET['id'])) { header('Location: ?action=admin_dashboard'); exit; }
        $course_id = $_GET['id'];
        
        require_once __DIR__ . '/../config/Database.php';
        require_once __DIR__ . '/../models/Curriculum.php';
        $db = (new Database())->getConnection();
        
        // Lấy thông tin khóa học (để hiển thị tên)
        $stmt = $db->prepare("SELECT id, title FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Lấy cấu trúc bài giảng hiện tại (Giống y hệt luồng Học viên lúc nãy)
        $curriculumModel = new Curriculum($db);
        $curriculum = $curriculumModel->getCourseCurriculum($course_id);
        
        require_once __DIR__ . '/../../views/admin/manage_content.php';
    }

    // 3. Hàm xử lý lưu Chương mới
    public function storeChapter() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            // BUG FIX: Thêm order_num để có thể sắp xếp chương học
            $stmt = $db->prepare("INSERT INTO chapters (course_id, title, order_num) VALUES (?, ?, ?)");
            $order_num = $_POST['order_num'] ?? 0;
            $stmt->execute([$_POST['course_id'], $_POST['title'], $order_num]);
            
            $_SESSION['success'] = "Đã thêm Chương học mới!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // 4. Hàm xử lý lưu Tài liệu/Bài giảng mới
    public function storeMaterial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra xem POST request có bị huỷ do vượt quá giới hạn dung lượng không
            if (empty($_POST)) {
                $_SESSION['error'] = "File tải lên quá lớn! Vui lòng chọn file nhỏ hơn hoặc cấu hình lại server.";
                header('Location: ?action=admin_manage_courses'); // Hoặc lấy referer nếu có
                exit();
            }

            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $content = $_POST['content'] ?? '';
            
            // Xử lý upload file nếu người dùng chọn loại "file"
            if ($_POST['type'] === 'file' && isset($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/materials/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['slide_file']['name'], PATHINFO_EXTENSION);
                $new_file_name = 'mat_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($_FILES['slide_file']['tmp_name'], $target_file)) {
                    $content = 'uploads/materials/' . $new_file_name;
                }
            }

            $stmt = $db->prepare("INSERT INTO materials (chapter_id, title, type, content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['chapter_id'], $_POST['title'], $_POST['type'], $content]);
            
            $_SESSION['success'] = "Đã thêm Tài liệu mới vào chương!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // Hàm hiển thị trang Tab Quản lý Khóa học
    public function manageCoursesList() {
        require_once __DIR__ . '/../config/Database.php';
        $db = (new Database())->getConnection();
        
        // Lấy danh sách khóa học
        $stmt = $db->query("SELECT * FROM courses ORDER BY id DESC");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Gọi file giao diện mới
        require_once __DIR__ . '/../../views/admin/manage_courses.php';
    }

    // Hàm xử lý CẬP NHẬT khóa học
    // Hàm xử lý CẬP NHẬT khóa học (Đã nâng cấp)
    public function updateCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $price = isset($_POST['price']) ? intval($_POST['price']) : 0;
            $description = $_POST['description'];
            $benefits = $_POST['benefits'] ?? '';
            $requirements = $_POST['requirements'] ?? '';
            $thumbnail_path = $_POST['old_thumbnail'];

            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/courses/';
                $file_extension = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
                $new_file_name = 'course_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file)) {
                    $thumbnail_path = 'uploads/courses/' . $new_file_name;
                }
            }

            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE courses SET title = ?, price = ?, description = ?, benefits = ?, requirements = ?, thumbnail = ? WHERE id = ?");
            $stmt->execute([$title, $price, $description, $benefits, $requirements, $thumbnail_path, $id]);

            $_SESSION['success'] = "Đã cập nhật khóa học thành công!";
            header('Location: ?action=admin_manage_courses');
            exit();
        }
    }

    // Hàm xử lý XÓA khóa học
    public function deleteCourse() {
        if (isset($_GET['id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            $_SESSION['success'] = "Khóa học đã được xóa khỏi hệ thống!";
            header('Location: ?action=admin_manage_courses');
            exit();
        }
    }

    // ==========================================
    // KHU VỰC QUẢN LÝ CHƯƠNG & BÀI GIẢNG (CRUD)
    // ==========================================

    // Cập nhật Chương
    public function updateChapter() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("UPDATE chapters SET title = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['id']]);
            
            $_SESSION['success'] = "Đã cập nhật tên Chương!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // Xóa Chương
    public function deleteChapter() {
        if (isset($_GET['id']) && isset($_GET['course_id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            // Xóa chương (Lưu ý: Nếu set khóa ngoại ON DELETE CASCADE trong DB thì bài giảng tự mất theo)
            $stmt = $db->prepare("DELETE FROM chapters WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            $_SESSION['success'] = "Đã xóa Chương học!";
            header('Location: ?action=admin_manage_content&id=' . $_GET['course_id']);
            exit();
        }
    }

    // Cập nhật Bài giảng
    public function updateMaterial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST)) {
                $_SESSION['error'] = "File tải lên quá lớn! Vui lòng chọn file nhỏ hơn hoặc cấu hình lại server.";
                header('Location: ?action=admin_manage_courses');
                exit();
            }

            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            
            $content = $_POST['content'] ?? '';
            
            // Xử lý upload file mới nếu người dùng chọn loại "file" và có file tải lên
            if ($_POST['type'] === 'file' && isset($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/uploads/materials/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['slide_file']['name'], PATHINFO_EXTENSION);
                $new_file_name = 'mat_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;

                if (move_uploaded_file($_FILES['slide_file']['tmp_name'], $target_file)) {
                    $content = 'uploads/materials/' . $new_file_name;
                }
            }

            $stmt = $db->prepare("UPDATE materials SET title = ?, type = ?, content = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['type'], $content, $_POST['id']]);
            
            $_SESSION['success'] = "Đã cập nhật Bài giảng!";
            header('Location: ?action=admin_manage_content&id=' . $_POST['course_id']);
            exit();
        }
    }

    // Xóa Bài giảng
    public function deleteMaterial() {
        if (isset($_GET['id']) && isset($_GET['course_id'])) {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("DELETE FROM materials WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            $_SESSION['success'] = "Đã xóa Bài giảng!";
            header('Location: ?action=admin_manage_content&id=' . $_GET['course_id']);
            exit();
        }
    }
}
?>