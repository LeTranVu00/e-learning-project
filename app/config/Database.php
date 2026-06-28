<?php
// File: app/config/Database.php

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    
    public $conn;

    public function __construct() {
        // Đọc thông tin kết nối từ biến môi trường (.env)
        $this->host     = $_ENV['DB_HOST']     ?? '127.0.0.1';
        $this->port     = $_ENV['DB_PORT']     ?? '3306';
        $this->db_name  = $_ENV['DB_NAME']     ?? 'e_learning_db';
        $this->username = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
    }

    public function getConnection() {
        $this->conn = null;

        try {
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