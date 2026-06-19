<?php
// File: app/controllers/ForumController.php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Forum.php';

class ForumController {
    
    // Trang chủ Diễn đàn
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tham gia diễn đàn!";
            header('Location: ?action=login');
            exit();
        }

        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'latest';
        
        // Nhận tín hiệu bộ lọc: 'all' (Tất cả) hoặc 'my_posts' (Của tôi)
        $filter = $_GET['filter'] ?? 'all'; 
        // Nếu chọn xem bài của mình thì gán ID, còn không thì để null
        $author_id = ($filter === 'my_posts') ? $_SESSION['user_id'] : null;

        $db = (new Database())->getConnection();
        $forumModel = new Forum($db);
        
        // Truyền cả 3 biến vào Model
        $posts = $forumModel->getAllPosts($search, $sort, $author_id);

        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/forum/index.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Xử lý lưu bài viết mới
    public function storePost() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);
            
            $title = $_POST['title'];
            $content = $_POST['content']; // Sẽ lấy từ CKEditor

            if($forumModel->createPost($_SESSION['user_id'], $title, $content)) {
                $_SESSION['success'] = "Đăng bài thảo luận thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra, không thể đăng bài.";
            }
            
            header('Location: ?action=forum');
            exit();
        }
    }

    // Xử lý CẬP NHẬT bài viết
    public function updatePost() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $post_id = $_POST['id'];
            $post = $forumModel->getPostById($post_id);

            // Kiểm tra quyền: Phải là chủ bài viết HOẶC là Admin
            if ($post && ($post['user_id'] == $_SESSION['user_id'] || $_SESSION['user_role'] === 'admin')) {
                $forumModel->updatePost($post_id, $_POST['title'], $_POST['content']);
                $_SESSION['success'] = "Đã cập nhật bài viết thành công!";
            } else {
                $_SESSION['error'] = "Bạn không có quyền sửa bài viết này!";
            }
            header('Location: ?action=forum');
            exit();
        }
    }

    // Xử lý XÓA bài viết
    public function deletePost() {
        if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $post_id = $_GET['id'];
            $post = $forumModel->getPostById($post_id);

            // Kiểm tra quyền: Phải là chủ bài viết HOẶC là Admin
            if ($post && ($post['user_id'] == $_SESSION['user_id'] || $_SESSION['user_role'] === 'admin')) {
                $forumModel->deletePost($post_id);
                $_SESSION['success'] = "Bài viết đã được xóa!";
            } else {
                $_SESSION['error'] = "Bạn không có quyền xóa bài viết này!";
            }
            header('Location: ?action=forum');
            exit();
        }
    }

    // Trang hiển thị Chi tiết Bài viết & Bình luận
    public function detail() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?action=login');
            exit();
        }

        $post_id = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $forumModel = new Forum($db);

        $post = $forumModel->getPostById($post_id);
        if (!$post) {
            $_SESSION['error'] = "Bài viết không tồn tại hoặc đã bị xóa!";
            header('Location: ?action=forum');
            exit();
        }

        // Lấy tất cả bình luận và Gom nhóm theo parent_id để vẽ Cây Đệ Quy
        $all_comments = $forumModel->getComments($post_id);
        $commentTree = [];
        foreach ($all_comments as $c) {
            $parentId = $c['parent_id'] ?: 0;
            $commentTree[$parentId][] = $c;
        }

        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/forum/detail.php'; // Mình sẽ tạo file này ở Bước 4
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Xử lý Gửi Bình luận
    public function storeComment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $post_id = $_POST['post_id'];
            $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
            $content = trim($_POST['content']);

            if (!empty($content)) {
                $forumModel->addComment($post_id, $_SESSION['user_id'], $parent_id, $content);
                $_SESSION['success'] = "Đã gửi bình luận!";
            }
            header('Location: ?action=forum_detail&id=' . $post_id);
            exit();
        }
    }
}
?>