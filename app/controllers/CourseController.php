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
        $courses = $courseModel->getAllCourses(); // Biến $courses này giờ đang chứa 1 mảng các khóa học

        // 3. Đưa dữ liệu ra View bằng cách nhúng các file giao diện vào
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/home.php';
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