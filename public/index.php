<?php
// File: public/index.php (Phần sửa đổi của Router)
// Bắt buộc phải khởi động Session để lưu trạng thái đăng nhập
session_start();

// Gọi autoload để tự động nạp toàn bộ thư viện trong folder vendor
require_once __DIR__ . '/../vendor/autoload.php';

// ... (Các dòng require Controller cũ ở dưới giữ nguyên)

require_once __DIR__ . '/../app/controllers/CourseController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php'; // Kéo thêm AuthController
require_once __DIR__ . '/../app/controllers/EnrollmentController.php';
$enrollmentController = new EnrollmentController();
require_once __DIR__ . '/../app/controllers/LearningController.php';
$learningController = new LearningController();

$courseController = new CourseController();
$authController = new AuthController(); // Khởi tạo AuthController

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch ($action) {
    case 'home':
        $courseController->index();
        break;
    case 'detail':
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $courseController->show($id);
        break;

    // ----- THÊM 2 ROUTE MỚI Ở ĐÂY -----
    case 'login':
        $authController->showLogin();
        break;
    case 'register':
        $authController->showRegister();
        break;
    // ----------------------------------

    // ----- ROUTE CHO GOOGLE SSO -----
    case 'google_login':
        $authController->googleLogin();
        break;
    case 'google_callback':
        $authController->googleCallback();
        break;
    // --------------------------------

    case 'logout':
        $authController->logout();
        break;

    //-----------------------------------
    // ----- ROUTE CHO GHI DANH KHÓA HỌC -----
    case 'enroll_course':
        $enrollmentController->enroll();
        break;
    
    // ----- ROUTE CHO TRANG KHÓA HỌC CỦA TÔI -----
    case 'my_courses':
        $enrollmentController->myCourses();
        break;
    
    // ----- ROUTE CHO TRANG HỌC KHÓA HỌC -----
    case 'learn':
        $learningController->index();
        break;

    case 'mark_done':
        $learningController->markDone();
        break;

    default:
        echo "<h1 style='text-align:center; margin-top:50px;'>404 - Không tìm thấy trang</h1>";
        break;
}
?>