<?php
// File: app/controllers/CourseController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Course.php';

class CourseController {
    
    // Hàm hiển thị trang chủ
    public function index() {
        // 1. Khởi tạo kết nối DB
        $database = new Database();
        $db = $database->getConnection();

        // 2. Gọi Model để lấy danh sách khóa học
        $courseModel = new Course($db);
        
        // Trang chủ chỉ lấy 6 khóa học mới nhất (hoặc nổi bật), không phân trang
        $limit = 6;
        $courses = $courseModel->getAllCourses($limit, 0);

        // Lấy danh mục khóa học
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($db);
        $categories = $categoryModel->getFeaturedCategories();

        // Lấy cảm nhận học viên
        require_once __DIR__ . '/../models/Testimonial.php';
        $testimonialModel = new Testimonial($db);
        $testimonials = $testimonialModel->getTestimonials();

        // 3. Đưa dữ liệu ra View bằng cách nhúng các file giao diện vào
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/home.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Hàm hiển thị danh sách tất cả khóa học có phân trang, tìm kiếm
    public function list() {
        $database = new Database();
        $db = $database->getConnection();
        $courseModel = new Course($db);
        
        // Lấy tham số tìm kiếm, lọc
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
        $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $category_info = null;

        if ($category_id) {
            require_once __DIR__ . '/../models/Category.php';
            $catModel = new Category($db);
            $category_info = $catModel->getCategoryById($category_id);
        }
        
        // Phân trang
        $limit = 6;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;
        
        $totalCourses = $courseModel->getTotalCoursesCount($search, '', $category_id);
        $totalPages = ceil($totalCourses / $limit);
        $courses = $courseModel->getAllCourses($limit, $offset, $search, $sort, '', $category_id);

        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/courses/index.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Hàm hiển thị chi tiết 1 khóa học
    public function show($id) {
        $database = new Database();
        $db = $database->getConnection();

        $courseModel = new Course($db);
        $course = $courseModel->getCourseById($id);

        // Nếu người dùng nhập bậy ID không có thật -> Báo lỗi hoặc quay về trang chủ
        if(!$course) {
            // BUG FIX: Dùng ?action=home thay vì /public/index.php để tránh 404
            echo "<script>alert('Khóa học không tồn tại!'); window.location.href='?action=home';</script>";
            return;
        }
        
        // THÊM ĐOẠN NÀY ĐỂ LẤY DANH SÁCH CHƯƠNG & BÀI GIẢNG:
        require_once __DIR__ . '/../models/Curriculum.php';
        $curriculumModel = new Curriculum($db);
        $curriculum = $curriculumModel->getCourseCurriculum($course['id']);
        

        // Đưa dữ liệu ra View chi tiết
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/courses/detail.php'; // Ta sẽ tạo file này ở bước sau
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }
}
?>