<?php
// File: app/models/Category.php

class Category {
    private $conn;

    public function __construct($db = null) {
        $this->conn = $db;
    }

    // Hàm lấy danh sách tất cả danh mục (cho Admin)
    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm lấy chi tiết 1 danh mục
    public function getCategoryById($id) {
        $query = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hàm tạo danh mục mới
    public function createCategory($name, $icon, $color) {
        $query = "INSERT INTO categories (name, icon, color) VALUES (:name, :icon, :color)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $name,
            ':icon' => $icon,
            ':color' => $color
        ]);
    }

    // Hàm cập nhật danh mục
    public function updateCategory($id, $name, $icon, $color) {
        $query = "UPDATE categories SET name = :name, icon = :icon, color = :color WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $name,
            ':icon' => $icon,
            ':color' => $color,
            ':id' => $id
        ]);
    }

    // Hàm xóa danh mục
    public function deleteCategory($id) {
        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Hàm lấy danh mục nổi bật (Cho trang chủ)
    public function getFeaturedCategories() {
        // Lấy tất cả danh mục kèm theo số lượng khóa học
        $query = "
            SELECT c.id, c.name, c.icon, c.color, COUNT(co.id) as course_count 
            FROM categories c
            LEFT JOIN courses co ON c.id = co.category_id
            GROUP BY c.id
            ORDER BY course_count DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
