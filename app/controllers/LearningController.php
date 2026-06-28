<?php
// File: app/controllers/LearningController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Curriculum.php';
require_once __DIR__ . '/../models/Progress.php';

class LearningController {

    public function index() {
        // 1. Phải đăng nhập mới được vào
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit();
        }

        // 2. Bắt ID khóa học trên thanh URL
        if (!isset($_GET['id'])) {
            header('Location: ?action=my_courses');
            exit();
        }

        $course_id = $_GET['id'];
        $user_id = $_SESSION['user_id'];
        $db = (new Database())->getConnection();
        
        // 3. Kiểm tra xem sinh viên này ĐÃ GHI DANH chưa? (Chặn gõ link bậy)
        $enrollmentModel = new Enrollment($db);
        if (!$enrollmentModel->isEnrolled($user_id, $course_id)) {
            $_SESSION['error'] = "Bạn chưa ghi danh khóa học này!";
            header('Location: ?action=my_courses');
            exit();
        }

        // 4. Lấy khung chương trình học
        $curriculumModel = new Curriculum($db);
        $curriculum = $curriculumModel->getCourseCurriculum($course_id);

        // 5. Lấy mảng các tài liệu đã hoàn thành
        $progressModel = new Progress($db);
        $completed_materials = $progressModel->getCompletedMaterials($user_id, $course_id);

        // 6. Hiển thị giao diện
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/courses/learn.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Hàm xử lý khi bấm nút Mark as done (Gọi ngầm bằng AJAX)
    public function markDone() {
        // BUG FIX: Set header JSON để JS có thể parse đúng kiểu
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || !isset($_POST['material_id'])) {
            // Trả về JSON thông báo lỗi
            echo json_encode(['success' => false, 'message' => 'Lỗi xác thực']);
            exit();
        }

        $db = (new Database())->getConnection();
        $progressModel = new Progress($db);
        
        // Lưu vào DB (hoặc xóa nếu đã tồn tại)
        $action = $progressModel->toggleDone($_SESSION['user_id'], $_POST['material_id']);
        
        // Trả về JSON báo thành công và hành động
        echo json_encode(['success' => true, 'action' => $action]);
        exit();
    }
}
?>