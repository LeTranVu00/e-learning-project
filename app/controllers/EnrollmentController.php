<?php
// File: app/controllers/EnrollmentController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Enrollment.php';

class EnrollmentController {
    
    public function enroll() {
        // 1. Kiểm tra đăng nhập (Chưa đăng nhập thì đá sang trang Login)
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để ghi danh khóa học!";
            header('Location: ?action=login');
            exit();
        }

        // 2. Lấy ID khóa học và ID người dùng
        if (isset($_GET['id'])) {
            $course_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];

            $db = (new Database())->getConnection();
            $enrollmentModel = new Enrollment($db);

            // 3. Thực hiện đăng ký
            if ($enrollmentModel->enrollUser($user_id, $course_id)) {
                // Đăng ký thành công -> Tạo thông báo và chuyển sang Khóa học của tôi
                $_SESSION['success'] = "Đăng ký khóa học thành công! Chào mừng bạn.";
                header('Location: ?action=my_courses');
                exit();
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra, vui lòng thử lại!";
                header('Location: ?action=course_detail&id=' . $course_id);
                exit();
            }
        }
    }

    // Hàm hiển thị trang Khóa học của tôi
    public function myCourses() {
        // Nếu chưa đăng nhập thì đá về trang Login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit();
        }

        $user_id = $_SESSION['user_id'];
        
        // Kết nối DB và lấy dữ liệu
        $db = (new Database())->getConnection();
        $enrollmentModel = new Enrollment($db);
        
        // Hứng danh sách khóa học vào biến $courses
        $courses = $enrollmentModel->getEnrolledCourses($user_id);

        // --- ĐOẠN MỚI THÊM: Tính % cho từng khóa học ---
        $progressModel = new Progress($db);
        foreach ($courses as $key => $course) {
            $courses[$key]['progress_percent'] = $progressModel->calculateProgress($user_id, $course['id']);
        }

        // Gọi View để hiển thị
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/courses/my_courses.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }
}
?>