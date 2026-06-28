<?php
// File: public/index.php (Phần sửa đổi của Router)
// Bắt buộc phải khởi động Session để lưu trạng thái đăng nhập
session_start();

// Gọi autoload để tự động nạp toàn bộ thư viện trong folder vendor
require_once __DIR__ . '/../vendor/autoload.php';

// Load biến môi trường từ file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// ... (Các dòng require Controller cũ ở dưới giữ nguyên)

require_once __DIR__ . '/../app/controllers/CourseController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php'; // Kéo thêm AuthController
require_once __DIR__ . '/../app/controllers/EnrollmentController.php';
require_once __DIR__ . '/../app/controllers/LearningController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/ForumController.php';
require_once __DIR__ . '/../app/controllers/PaymentController.php';

$courseController = new CourseController();
$authController = new AuthController(); // Khởi tạo AuthController
$enrollmentController = new EnrollmentController();
$learningController = new LearningController();
$forumController = new ForumController();



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
    // Route xử lý form POST đăng nhập / đăng ký thường
    case 'handle_login':
        $authController->handleLogin();
        break;
    case 'handle_register':
        $authController->handleRegister();
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
    // ----- ROUTE CHO THANH TOÁN VNPAY -----
    case 'pay':
        $paymentController = new PaymentController();
        $paymentController->createPayment();
        break;
    case 'vnpay_return':
        $paymentController = new PaymentController();
        $paymentController->vnpayReturn();
        break;
    // ------------------------------------

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
    // ----- ROUTE CHO MARK DONE -----
    case 'mark_done':
        $learningController->markDone();
        break;
    //Route cho trang Dashboard Admin
    case 'admin_dashboard':
        $adminController = new AdminController();
        $adminController->dashboard();
        break;
    
    //Route cho trang tạo khóa học mới
    case 'admin_create_course':
        $adminController = new AdminController();
        $adminController->createCourse();
        break;
    
    //Route cho xử lý lưu khóa học mới (Có upload ảnh)
    case 'admin_store_course':
        $adminController = new AdminController();
        $adminController->storeCourse();
        break;
    
    //Route cho trang quản lý nội dung khóa học (Chương & Bài học)
    case 'admin_manage_content':
        $adminController = new AdminController();
        $adminController->manageContent();
        break;

    //Route cho xử lý lưu Chương mới
    case 'admin_store_chapter':
        $adminController = new AdminController();
        $adminController->storeChapter();
        break;

    //Route cho xử lý lưu Tài liệu/Bài học mới
    case 'admin_store_material':
        $adminController = new AdminController();
        $adminController->storeMaterial();
        break;

    //Route cho trang quản lý danh sách khóa học
    case 'admin_manage_courses':
        $adminController = new AdminController();
        $adminController->manageCoursesList();
        break;
    
    //Route cho xử lý cập nhật khóa học
    case 'admin_update_course':
        $adminController = new AdminController();
        $adminController->updateCourse();
        break;
    
    //Route cho xử lý xóa khóa học
    case 'admin_delete_course':
        $adminController = new AdminController();
        $adminController->deleteCourse();
        break;
    
    //Route cho xử lý cập nhật Chương
    case 'admin_update_chapter':
        $adminController = new AdminController();
        $adminController->updateChapter();
        break;
    case 'admin_delete_chapter':
        $adminController = new AdminController();
        $adminController->deleteChapter();
        break;
    case 'admin_update_material':
        $adminController = new AdminController();
        $adminController->updateMaterial();
        break;
    case 'admin_delete_material':
        $adminController = new AdminController();
        $adminController->deleteMaterial();
        break;
    
    //Route cho trang quản lý danh sách bài viết thảo luận
    case 'forum':
        $forumController->index();
        break;
    case 'forum_store_post':
        $forumController->storePost();
        break;

    // route cho xử lý cập nhật bài viết
    case 'forum_update_post':
        $forumController->updatePost();
        break;
    case 'forum_delete_post':
        $forumController->deletePost();
        break;


    //Route cho trang chi tiết bài viết thảo luận
    case 'forum_detail':
        $forumController->detail();
        break;
    case 'forum_store_comment':
        $forumController->storeComment();
        break;
        
    default:
        echo "<h1 style='text-align:center; margin-top:50px;'>404 - Không tìm thấy trang</h1>";
        break;
}
?>