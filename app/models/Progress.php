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

    // Lấy ra danh sách ID và Điểm số các tài liệu mà User ĐÃ hoàn thành
    public function getCompletedMaterialsWithScores($user_id, $course_id) {
        $query = "SELECT mc.material_id, mc.score FROM material_completions mc
                  JOIN materials m ON mc.material_id = m.id
                  JOIN chapters c ON m.chapter_id = c.id
                  WHERE mc.user_id = :user_id AND c.course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[$row['material_id']] = $row['score'];
        }
        return $results;
    }

    // Bật/Tắt trạng thái hoàn thành (Toggle)
    public function toggleDone($user_id, $material_id) {
        $check = "SELECT id FROM material_completions WHERE user_id = :uid AND material_id = :mid";
        $stmt = $this->conn->prepare($check);
        $stmt->execute(['uid' => $user_id, 'mid' => $material_id]);
        
        if($stmt->rowCount() > 0) {
            // Đã có -> Xóa đi
            $del = "DELETE FROM material_completions WHERE user_id = :uid AND material_id = :mid";
            $this->conn->prepare($del)->execute(['uid' => $user_id, 'mid' => $material_id]);
            return 'removed';
        } else {
            // Chưa có -> Thêm vào
            $insert = "INSERT INTO material_completions (user_id, material_id) VALUES (:uid, :mid)";
            $this->conn->prepare($insert)->execute(['uid' => $user_id, 'mid' => $material_id]);
            return 'added';
        }
    }

    // Bật trạng thái hoàn thành và lưu điểm (Dành cho Quiz)
    public function saveQuizScore($user_id, $material_id, $score) {
        $check = "SELECT id FROM material_completions WHERE user_id = :uid AND material_id = :mid";
        $stmt = $this->conn->prepare($check);
        $stmt->execute(['uid' => $user_id, 'mid' => $material_id]);
        
        if($stmt->rowCount() > 0) {
            // Đã có -> Cập nhật điểm
            $update = "UPDATE material_completions SET score = :score WHERE user_id = :uid AND material_id = :mid";
            $this->conn->prepare($update)->execute(['uid' => $user_id, 'mid' => $material_id, 'score' => $score]);
        } else {
            // Chưa có -> Thêm vào cùng điểm
            $insert = "INSERT INTO material_completions (user_id, material_id, score) VALUES (:uid, :mid, :score)";
            $this->conn->prepare($insert)->execute(['uid' => $user_id, 'mid' => $material_id, 'score' => $score]);
        }
    }

    // Hàm tính % hoàn thành khóa học của một User
    public function calculateProgress($user_id, $course_id) {
        // 1. Đếm TỔNG SỐ tài liệu có trong khóa học này
        $q_total = "SELECT COUNT(m.id) FROM materials m 
                    JOIN chapters c ON m.chapter_id = c.id 
                    WHERE c.course_id = :course_id";
        $stmt_total = $this->conn->prepare($q_total);
        $stmt_total->execute(['course_id' => $course_id]);
        $total_materials = $stmt_total->fetchColumn();

        // Tránh lỗi chia cho 0 nếu khóa học chưa có tài liệu nào
        if ($total_materials == 0) return 0; 

        // 2. Đếm SỐ TÀI LIỆU ĐÃ HOÀN THÀNH của User này trong khóa đó
        $q_done = "SELECT COUNT(mc.id) FROM material_completions mc 
                   JOIN materials m ON mc.material_id = m.id 
                   JOIN chapters c ON m.chapter_id = c.id 
                   WHERE c.course_id = :course_id AND mc.user_id = :user_id";
        $stmt_done = $this->conn->prepare($q_done);
        $stmt_done->execute(['course_id' => $course_id, 'user_id' => $user_id]);
        $done_materials = $stmt_done->fetchColumn();

        // 3. Tính % và làm tròn số
        $percent = ($done_materials / $total_materials) * 100;
        return round($percent);
    }
}
?>