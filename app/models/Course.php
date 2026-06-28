<?php
// File: app/models/Course.php

class Course {
    private $conn;

    // Nhận kết nối CSDL từ bên ngoài truyền vào
    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm lấy tất cả khóa học
    public function getAllCourses() {
        // Câu lệnh SQL lấy dữ liệu, sắp xếp mới nhất lên đầu
        $query = "SELECT * FROM courses ORDER BY created_at DESC";
        
        // Chuẩn bị và thực thi (Dùng prepare để chuẩn PDO)
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Trả về một mảng chứa dữ liệu
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // Lấy chi tiết 1 khóa học dựa vào ID
    public function getCourseById($id) {
        // Dùng tham số :id để tránh SQL Injection
        $query = "SELECT * FROM courses WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Gán giá trị thực tế vào tham số :id
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Lấy 1 dòng duy nhất (FETCH_ASSOC)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hàm thêm khóa học mới vào DB
    // Hàm thêm khóa học mới vào DB (Đã cập nhật)
    public function createCourse($title, $description, $thumbnail, $benefits, $requirements, $price = 0) {
        $query = "INSERT INTO courses (title, price, description, thumbnail, benefits, requirements) 
                  VALUES (:title, :price, :description, :thumbnail, :benefits, :requirements)";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':title' => $title,
            ':price' => $price,
            ':description' => $description,
            ':thumbnail' => $thumbnail,
            ':benefits' => $benefits,
            ':requirements' => $requirements
        ]);
    }
}
?>