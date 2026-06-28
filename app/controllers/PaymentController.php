<?php
// File: app/controllers/PaymentController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Enrollment.php';

class PaymentController {

    public function createPayment() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thực hiện thanh toán!";
            header('Location: ?action=login');
            exit();
        }

        if (!isset($_GET['id'])) {
            header('Location: ?action=home');
            exit();
        }

        $course_id = intval($_GET['id']);
        $user_id = $_SESSION['user_id'];

        $db = (new Database())->getConnection();
        $courseModel = new Course($db);
        $course = $courseModel->getCourseById($course_id);

        if (!$course) {
            $_SESSION['error'] = "Khóa học không tồn tại!";
            header('Location: ?action=home');
            exit();
        }

        // Nếu khóa học miễn phí (giá = 0), tự động ghi danh luôn thay vì qua VNPAY
        if (!isset($course['price']) || $course['price'] <= 0) {
            $enrollmentModel = new Enrollment($db);
            if ($enrollmentModel->enrollUser($user_id, $course_id)) {
                $_SESSION['success'] = "Đăng ký khóa học miễn phí thành công!";
                header('Location: ?action=my_courses');
            } else {
                $_SESSION['error'] = "Bạn đã đăng ký khóa học này rồi hoặc có lỗi xảy ra!";
                header('Location: ?action=course_detail&id=' . $course_id);
            }
            exit();
        }

        // Tạo Transaction Reference (Mã giao dịch nội bộ duy nhất)
        $vnp_TxnRef = time() . "_" . $user_id . "_" . $course_id;
        $amount = $course['price'];

        // Lưu bản nháp (pending) vào CSDL trước
        $stmt = $db->prepare("INSERT INTO payments (user_id, course_id, amount, vnp_txn_ref, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $course_id, $amount, $vnp_TxnRef]);

        // Cấu hình VNPAY từ biến môi trường
        $vnp_TmnCode = $_ENV['VNP_TMN_CODE'] ?? '';
        $vnp_HashSecret = $_ENV['VNP_HASH_SECRET'] ?? '';
        $vnp_Url = $_ENV['VNP_URL'] ?? 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $vnp_Returnurl = $_ENV['VNP_RETURN_URL'] ?? 'http://localhost/e-learning-project/public/index.php?action=vnpay_return';

        $vnp_OrderInfo = "Thanh toan khoa hoc: " . $course['title'];
        $vnp_OrderType = 'other';
        $vnp_Amount = $amount * 100; // VNPAY yêu cầu nhân 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        // Thiết lập múi giờ Việt Nam để tạo ngày giờ chính xác cho VNPAY
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $startTime = date("YmdHis");
        $expireTime = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expireTime
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // Chuyển hướng sang cổng VNPAY
        header('Location: ' . $vnp_Url);
        exit();
    }

    public function vnpayReturn() {
        $vnp_HashSecret = $_ENV['VNP_HASH_SECRET'] ?? '';
        
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']); // Bỏ type nếu có

        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? '';
        $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? '';
        $vnp_TransactionNo = $inputData['vnp_TransactionNo'] ?? '';

        $db = (new Database())->getConnection();

        if ($secureHash === $vnp_SecureHash) {
            // Chữ ký hợp lệ
            if ($vnp_ResponseCode == '00') {
                // Thanh toán thành công
                // 1. Cập nhật trạng thái payment
                $stmt = $db->prepare("UPDATE payments SET status = 'success', vnp_transaction_no = ?, vnp_response_code = ? WHERE vnp_txn_ref = ?");
                $stmt->execute([$vnp_TransactionNo, $vnp_ResponseCode, $vnp_TxnRef]);

                // 2. Lấy user_id và course_id từ db payments để ghi danh
                $stmt = $db->prepare("SELECT user_id, course_id FROM payments WHERE vnp_txn_ref = ?");
                $stmt->execute([$vnp_TxnRef]);
                $payment = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($payment) {
                    $enrollmentModel = new Enrollment($db);
                    // Dùng @ để bỏ qua warning nếu duplicate key (trường hợp user bấm F5)
                    @$enrollmentModel->enrollUser($payment['user_id'], $payment['course_id']);
                }

                $_SESSION['success'] = "Thanh toán thành công! Chào mừng bạn đến với khóa học.";
                header('Location: ?action=my_courses');
                exit();
            } else {
                // Lỗi thanh toán
                $stmt = $db->prepare("UPDATE payments SET status = 'failed', vnp_transaction_no = ?, vnp_response_code = ? WHERE vnp_txn_ref = ?");
                $stmt->execute([$vnp_TransactionNo, $vnp_ResponseCode, $vnp_TxnRef]);

                $_SESSION['error'] = "Thanh toán bị hủy hoặc có lỗi xảy ra (Mã lỗi: $vnp_ResponseCode).";
                header('Location: ?action=home'); // Nên redirect về course detail, nhưng tạm về home
                exit();
            }
        } else {
            $_SESSION['error'] = "Sai chữ ký bảo mật (Invalid signature).";
            header('Location: ?action=home');
            exit();
        }
    }
}
?>
