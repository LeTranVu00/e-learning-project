<?php
// File: app/models/Enrollment.php

class Enrollment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kiểm tra xem User đã đăng ký khóa này chưa
    public function isEnrolled($user_id, $course_id) {
        $query = "SELECT id FROM enrollments WHERE user_id = :user_id AND course_id = :course_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Thực hiện Ghi danh
    public function enrollUser($user_id, $course_id) {
        // Nếu đã đăng ký rồi thì không làm gì thêm, báo thành công luôn
        if ($this->isEnrolled($user_id, $course_id)) {
            return true; 
        }

        $query = "INSERT INTO enrollments (user_id, course_id) VALUES (:user_id, :course_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        
        return $stmt->execute();
    }

    // Hàm lấy danh sách khóa học đã đăng ký của 1 User
    public function getEnrolledCourses($user_id) {
        $query = "SELECT c.*, e.enrolled_at 
                  FROM courses c 
                  JOIN enrollments e ON c.id = e.course_id 
                  WHERE e.user_id = :user_id 
                  ORDER BY e.enrolled_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>