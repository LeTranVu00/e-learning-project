<?php
// File: app/core/BaseController.php

require_once __DIR__ . '/../config/Database.php';

class BaseController {
    protected $db;

    public function __construct() {
        // Tự động khởi tạo kết nối database cho mọi controller kế thừa
        $this->db = (new Database())->getConnection();
    }

    /**
     * Render một view với data truyền vào
     * @param string $viewPath (ví dụ: 'views/home.php')
     * @param array $data Dữ liệu truyền sang view
     * @param bool $useLayout Sử dụng header/footer hay không
     */
    protected function render($viewPath, $data = [], $useLayout = true) {
        // Biến các key của mảng $data thành các biến độc lập
        // Ví dụ: ['course' => $course] sẽ tạo ra biến $course
        extract($data);

        if ($useLayout) {
            require_once __DIR__ . '/../../views/layouts/header.php';
        }

        require_once __DIR__ . '/../../' . $viewPath;

        if ($useLayout) {
            require_once __DIR__ . '/../../views/layouts/footer.php';
        }
    }

    /**
     * Chuyển hướng người dùng
     */
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }

    /**
     * Trả về JSON cho API/AJAX
     */
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>
