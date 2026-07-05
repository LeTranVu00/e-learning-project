<?php
// File: app/controllers/LearningController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Curriculum.php';
require_once __DIR__ . '/../models/Progress.php';

class LearningController {

    public function index() {
        // 1. Phải đăng nhập mới được vào
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit();
        }

        // 2. Bắt ID khóa học trên thanh URL
        if (!isset($_GET['id'])) {
            header('Location: ?action=my_courses');
            exit();
        }

        $course_id = $_GET['id'];
        $user_id = $_SESSION['user_id'];
        $db = (new Database())->getConnection();
        
        // 3. Kiểm tra xem sinh viên này ĐÃ GHI DANH chưa? (Chặn gõ link bậy)
        $enrollmentModel = new Enrollment($db);
        if (!$enrollmentModel->isEnrolled($user_id, $course_id)) {
            $_SESSION['error'] = "Bạn chưa ghi danh khóa học này!";
            header('Location: ?action=my_courses');
            exit();
        }

        // 4. Lấy khung chương trình học
        $curriculumModel = new Curriculum($db);
        $curriculum = $curriculumModel->getCourseCurriculum($course_id);

        // 5. Lấy mảng các tài liệu đã hoàn thành
        $progressModel = new Progress($db);
        $completed_materials = $progressModel->getCompletedMaterials($user_id, $course_id);

        // 6. Hiển thị giao diện
        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/courses/learn.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Hàm xử lý khi bấm nút Mark as done (Gọi ngầm bằng AJAX)
    public function markDone() {
        // BUG FIX: Set header JSON để JS có thể parse đúng kiểu
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || !isset($_POST['material_id'])) {
            // Trả về JSON thông báo lỗi
            echo json_encode(['success' => false, 'message' => 'Lỗi xác thực']);
            exit();
        }

        $db = (new Database())->getConnection();
        $progressModel = new Progress($db);
        
        // Lưu vào DB (hoặc xóa nếu đã tồn tại)
        $action = $progressModel->toggleDone($_SESSION['user_id'], $_POST['material_id']);
        
        // Trả về JSON báo thành công và hành động
        echo json_encode(['success' => true, 'action' => $action]);
        exit();
    }

    // Hàm xử lý nộp bài trắc nghiệm (AJAX)
    public function submitQuiz() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id']) || !isset($_POST['material_id'])) {
            echo json_encode(['success' => false, 'message' => 'Lỗi xác thực']);
            exit();
        }

        $db = (new Database())->getConnection();
        
        // 1. Lấy bài trắc nghiệm từ DB
        $stmt = $db->prepare("SELECT content, type FROM materials WHERE id = ?");
        $stmt->execute([$_POST['material_id']]);
        $material = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$material || $material['type'] !== 'quiz') {
            echo json_encode(['success' => false, 'message' => 'Bài học không hợp lệ']);
            exit();
        }

        // 2. Chấm điểm
        $quizData = json_decode($material['content'], true);
        if (!is_array($quizData) || count($quizData) === 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu trắc nghiệm bị lỗi']);
            exit();
        }

        $totalQuestions = count($quizData);
        $correctAnswers = 0;
        $results = [];

        foreach ($quizData as $index => $q) {
            $userAns = isset($_POST['ans_' . $index]) ? (int)$_POST['ans_' . $index] : -1;
            $isCorrect = $userAns === (int)$q['correct_index'];
            
            if ($isCorrect) {
                $correctAnswers++;
            }
            
            $results[$index] = [
                'is_correct' => $isCorrect,
                'correct_index' => (int)$q['correct_index'],
                'user_ans' => $userAns
            ];
        }

        // Tính theo thang điểm 100
        $score = round(($correctAnswers / $totalQuestions) * 100);
        $passed = $score >= 50; // Qua môn nếu >= 50đ

        // 3. Lưu lịch sử
        if ($passed) {
            $progressModel = new Progress($db);
            $progressModel->saveQuizScore($_SESSION['user_id'], $_POST['material_id'], $score);
        }

        echo json_encode([
            'success' => true,
            'score' => $score,
            'passed' => $passed,
            'results' => $results,
            'message' => $passed ? 'Bạn đã qua bài kiểm tra!' : 'Bạn chưa đạt điểm yêu cầu (50 điểm).'
        ]);
        exit();
    }
}
?>