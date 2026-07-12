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
    public function updateUser($id, $fullname, $role, $phone = null, $address = null, $bio = null) {
        $query = "UPDATE users SET fullname = :fullname, role = :role";
        
        if ($phone !== null) $query .= ", phone = :phone";
        if ($address !== null) $query .= ", address = :address";
        if ($bio !== null) $query .= ", bio = :bio";
        
        $query .= " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':role', $role);
        
        if ($phone !== null) $stmt->bindParam(':phone', $phone);
        if ($address !== null) $stmt->bindParam(':address', $address);
        if ($bio !== null) $stmt->bindParam(':bio', $bio);
        
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Cập nhật Hồ sơ cá nhân (dành cho người dùng)
    public function updateProfile($id, $fullname, $phone, $address, $bio, $avatar = null) {
        $query = "UPDATE users SET fullname = :fullname, phone = :phone, address = :address, bio = :bio";
        if ($avatar !== null) {
            $query .= ", avatar = :avatar";
        }
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':bio', $bio);
        if ($avatar !== null) {
            $stmt->bindParam(':avatar', $avatar);
        }
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Xóa người dùng và các dữ liệu liên quan
    public function deleteUser($id) {
        try {
            $this->conn->beginTransaction();
            
            // Xóa các dữ liệu tham chiếu
            $this->conn->prepare("DELETE FROM material_completions WHERE user_id = :id")->execute([':id' => $id]);
            $this->conn->prepare("DELETE FROM enrollments WHERE user_id = :id")->execute([':id' => $id]);
            $this->conn->prepare("DELETE FROM comments WHERE user_id = :id")->execute([':id' => $id]);
            $this->conn->prepare("DELETE FROM forum_posts WHERE user_id = :id")->execute([':id' => $id]);
            $this->conn->prepare("DELETE FROM system_logs WHERE user_id = :id")->execute([':id' => $id]);
            
            // Cuối cùng xóa user
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    // --- CÁC HÀM KHÔI PHỤC MẬT KHẨU ---

    // Lưu mã xác nhận khôi phục mật khẩu
    public function saveResetToken($email, $token, $expiry) {
        // Sử dụng DATE_ADD(NOW(), INTERVAL 1 HOUR) của MySQL để đồng bộ múi giờ
        $query = "UPDATE users SET reset_token = :token, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    // Tìm người dùng theo mã xác nhận khôi phục mật khẩu
    public function findByResetToken($token) {
        $query = "SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật mật khẩu mới và xóa token
    public function updatePassword($id, $hashed_password) {
        $query = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>