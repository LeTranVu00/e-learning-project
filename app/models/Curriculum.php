<?php
// File: app/models/Curriculum.php

class Curriculum {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy toàn bộ cấu trúc: Chương -> Tài liệu bên trong
    public function getCourseCurriculum($course_id) {
        // 1. Lấy tất cả các Chương (Chapters) của khóa học này
        $query = "SELECT * FROM chapters WHERE course_id = :course_id ORDER BY order_num ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Chạy vòng lặp: Với mỗi Chương, lấy các Tài liệu (Materials) của nó nhét vào
        foreach ($chapters as $key => $chapter) {
            $q_materials = "SELECT * FROM materials WHERE chapter_id = :chapter_id";
            $stmt_m = $this->conn->prepare($q_materials);
            $stmt_m->bindParam(':chapter_id', $chapter['id']);
            $stmt_m->execute();
            
            // Nhét kết quả vào một mảng con tên là 'materials'
            $chapters[$key]['materials'] = $stmt_m->fetchAll(PDO::FETCH_ASSOC);
        }

        return $chapters; // Trả về cục dữ liệu hoàn chỉnh
    }
}
?>