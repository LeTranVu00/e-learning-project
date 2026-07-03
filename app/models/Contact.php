<?php
// File: app/models/Contact.php

class Contact {
    private $conn;

    public function __construct($db = null) {
        $this->conn = $db;
    }

    // Gửi liên hệ mới
    public function createContact($name, $email, $subject, $message) {
        $query = "INSERT INTO contacts (name, email, subject, message) VALUES (:name, :email, :subject, :message)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message
        ]);
    }

    // Lấy tất cả liên hệ (Dành cho Admin)
    public function getAllContacts() {
        $query = "SELECT * FROM contacts ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết liên hệ
    public function getContactById($id) {
        $query = "SELECT * FROM contacts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Đánh dấu đã phản hồi
    public function markAsResolved($id) {
        $query = "UPDATE contacts SET status = 'resolved' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
    
    // Đếm số lượng tin nhắn chưa xử lý
    public function getPendingCount() {
        $query = "SELECT COUNT(*) as count FROM contacts WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}
?>
