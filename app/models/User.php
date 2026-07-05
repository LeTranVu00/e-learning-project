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

    // Lấy danh sách người dùng (Có phân trang, lọc, sắp xếp)
    public function getAllUsers($limit = null, $offset = null, $search = '', $role = 'all', $sort = 'latest') {
        $query = "SELECT * FROM users WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (fullname LIKE :search OR email LIKE :search)";
        }
        
        if ($role !== 'all' && !empty($role)) {
            $query .= " AND role = :role";
        }
        
        if ($sort === 'oldest') {
            $query .= " ORDER BY created_at ASC";
        } else {
            $query .= " ORDER BY created_at DESC";
        }
        
        if ($limit !== null && $offset !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }
        if ($role !== 'all' && !empty($role)) {
            $stmt->bindValue(':role', $role);
        }
        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số người dùng
    public function getTotalUsersCount($search = '', $role = 'all') {
        $query = "SELECT COUNT(*) FROM users WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (fullname LIKE :search OR email LIKE :search)";
        }
        if ($role !== 'all' && !empty($role)) {
            $query .= " AND role = :role";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%");
        }
        if ($role !== 'all' && !empty($role)) {
            $stmt->bindValue(':role', $role);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Lấy chi tiết người dùng
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật người dùng (dành cho Admin)
    public function updateUser($id, $fullname, $role) {
        $query = "UPDATE users SET fullname = :fullname, role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Cập nhật Hồ sơ cá nhân (dành cho người dùng)
    public function updateProfile($id, $fullname, $phone, $address, $bio) {
        $query = "UPDATE users SET fullname = :fullname, phone = :phone, address = :address, bio = :bio WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Xóa người dùng
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>