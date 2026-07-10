<?php
// File: app/utils/AuditLogger.php

class AuditLogger {
    /**
     * Ghi lại nhật ký hệ thống
     * 
     * @param string $action Hành động (ví dụ: 'create', 'update', 'delete', 'login')
     * @param string $description Mô tả chi tiết hành động
     * @param string|null $entity_type Loại đối tượng bị ảnh hưởng (ví dụ: 'course', 'user', 'category')
     * @param int|null $entity_id ID của đối tượng
     */
    public static function log($action, $description, $entity_type = null, $entity_id = null) {
        try {
            require_once __DIR__ . '/../config/Database.php';
            $db = (new Database())->getConnection();

            $user_id = $_SESSION['user_id'] ?? null;
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

            $stmt = $db->prepare("
                INSERT INTO system_logs (user_id, action, description, entity_type, entity_id, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $user_id,
                $action,
                $description,
                $entity_type,
                $entity_id,
                $ip_address
            ]);
            
            return true;
        } catch (Exception $e) {
            // Log lỗi hệ thống ra file nếu cần thiết, không làm gián đoạn luồng xử lý chính
            error_log("AuditLogger Error: " . $e->getMessage());
            return false;
        }
    }
}
