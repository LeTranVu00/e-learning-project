<?php
// File: app/models/User.php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tìm user theo Email
    public function findByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo tài khoản mới bằng dữ liệu từ Google (Không cần mật khẩu)
    public function createGoogleUser($fullname, $email, $google_id, $avatar = null) {
        $query = "INSERT INTO users (fullname, email, google_id, avatar, role) VALUES (:fullname, :email, :google_id, :avatar, 'student')";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':google_id', $google_id);
        $stmt->bindParam(':avatar', $avatar);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId(); // Trả về ID vừa tạo
        }
        return false;
    }

    // Tạo tài khoản mới bằng email + mật khẩu thường
    public function createUser($fullname, $email, $hashed_password) {
        $query = "INSERT INTO users (fullname, email, password, role) VALUES (:fullname, :email, :password, 'student')";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
}
?>