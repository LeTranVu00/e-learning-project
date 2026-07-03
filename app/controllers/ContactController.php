<?php
// File: app/controllers/ContactController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Contact.php';

class ContactController {
    
    // Hiển thị form liên hệ
    public function index() {
        // Có thể truyền thêm dữ liệu vào trang liên hệ nếu cần
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/contact.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Xử lý submit form liên hệ
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = 'Liên hệ từ trang chủ';
            $message = trim($_POST['message'] ?? '');

            if (empty($name) || empty($email) || empty($message)) {
                $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
                header("Location: ?action=home#contact");
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email không đúng định dạng!';
                header("Location: ?action=home#contact");
                exit;
            }

            $database = new Database();
            $db = $database->getConnection();
            $contactModel = new Contact($db);

            if ($contactModel->createContact($name, $email, $subject, $message)) {
                $_SESSION['success'] = 'Tin nhắn của bạn đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại sau.';
            }
            
            header("Location: ?action=home#contact");
            exit;
        }
    }
}
?>
