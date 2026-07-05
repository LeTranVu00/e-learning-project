<?php
// File: app/controllers/CartController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Course.php';

class CartController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Hiển thị trang giỏ hàng
    public function index() {
        $db = (new Database())->getConnection();
        $courseModel = new Course($db);
        
        $cartItems = [];
        $totalPrice = 0;
        
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $course_id) {
                $course = $courseModel->getCourseById($course_id);
                if ($course) {
                    $cartItems[] = $course;
                    $totalPrice += isset($course['price']) ? $course['price'] : 0;
                }
            }
        }
        
        require_once __DIR__ . '/../../views/cart/index.php';
    }

    // Thêm vào giỏ (AJAX)
    public function add() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;

        if ($course_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID khóa học không hợp lệ']);
            exit();
        }

        if (!in_array($course_id, $_SESSION['cart'])) {
            $_SESSION['cart'][] = $course_id;
            echo json_encode([
                'success' => true, 
                'message' => 'Đã thêm vào giỏ hàng',
                'cart_count' => count($_SESSION['cart'])
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Khóa học đã có trong giỏ hàng',
                'cart_count' => count($_SESSION['cart'])
            ]);
        }
        exit();
    }

    // Xóa khỏi giỏ (AJAX)
    public function remove() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $course_id = isset($input['course_id']) ? intval($input['course_id']) : 0;

        if (($key = array_search($course_id, $_SESSION['cart'])) !== false) {
            unset($_SESSION['cart'][$key]);
            // Re-index array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            
            // Tính lại tổng tiền
            $db = (new Database())->getConnection();
            $courseModel = new Course($db);
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $id) {
                $course = $courseModel->getCourseById($id);
                if ($course) {
                    $totalPrice += isset($course['price']) ? $course['price'] : 0;
                }
            }

            echo json_encode([
                'success' => true, 
                'message' => 'Đã xóa khỏi giỏ hàng',
                'cart_count' => count($_SESSION['cart']),
                'total_price' => number_format($totalPrice, 0, ',', '.') . 'đ'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy khóa học trong giỏ']);
        }
        exit();
    }
}
?>
