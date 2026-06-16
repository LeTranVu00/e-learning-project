<?php
// File: app/models/Progress.php

class Progress {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy ra danh sách ID các tài liệu mà User ĐÃ hoàn thành trong khóa học này
    public function getCompletedMaterials($user_id, $course_id) {
        // Dùng JOIN để móc nối từ Tài liệu -> Chương -> Khóa học
        $query = "SELECT mc.material_id FROM material_completions mc
                  JOIN materials m ON mc.material_id = m.id
                  JOIN chapters c ON m.chapter_id = c.id
                  WHERE mc.user_id = :user_id AND c.course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        // Trả về một mảng 1 chiều chứa các ID (Ví dụ: [1, 2, 5])
        return $stmt->fetchAll(PDO::FETCH_COLUMN); 
    }

    // Đánh dấu hoàn thành (Insert vào DB)
    public function markAsDone($user_id, $material_id) {
        // Kiểm tra xem đã click trước đó chưa (chống spam)
        $check = "SELECT id FROM material_completions WHERE user_id = :uid AND material_id = :mid";
        $stmt = $this->conn->prepare($check);
        $stmt->execute(['uid' => $user_id, 'mid' => $material_id]);
        if($stmt->rowCount() > 0) return true;

        // Nếu chưa thì chèn dòng mới
        $query = "INSERT INTO material_completions (user_id, material_id) VALUES (:uid, :mid)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['uid' => $user_id, 'mid' => $material_id]);
    }
}
?>