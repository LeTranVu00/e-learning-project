<?php
// File: app/config/Database.php

class Database {
    private $host = "127.0.0.1"; 
    private $port = "3307"; // Thêm khai báo port 3307 ở đây
    private $db_name = "e_learning_db";
    private $username = "root";
    private $password = ""; 
    
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Đưa thêm biến port vào chuỗi DSN kết nối
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8mb4");
            
        } catch(PDOException $exception) {
            echo "Lỗi kết nối CSDL: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>