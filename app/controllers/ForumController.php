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
        
        // Phân trang
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;
        
        $totalPosts = $forumModel->getTotalPostsCount($search, $author_id);
        $totalPages = ceil($totalPosts / $limit);
        
        // Truyền tham số vào Model
        $posts = $forumModel->getAllPosts($search, $sort, $author_id, '', $limit, $offset);

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
            if (isset($_GET['admin'])) {
                header('Location: ?action=admin_post_comments&id=' . $post_id);
            } else {
                header('Location: ?action=forum');
            }
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
            if (isset($_GET['admin'])) {
                echo "<script>window.parent.postMessage({action: 'close_modal_and_reload'}, '*');</script>";
                exit();
            } else {
                header('Location: ?action=forum');
            }
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

        // Lấy sort option cho bình luận: 'latest' hoặc 'popular'
        $comment_sort = in_array($_GET['csort'] ?? '', ['latest', 'popular']) ? $_GET['csort'] : 'latest';

        // Lấy tất cả bình luận kèm dữ liệu like/dislike
        $all_comments = $forumModel->getComments($post_id, $_SESSION['user_id'], $comment_sort);
        $commentTree = [];
        foreach ($all_comments as $c) {
            $parentId = $c['parent_id'] ?: 0;
            $commentTree[$parentId][] = $c;
        }

        require_once __DIR__ . '/../../views/layouts/header.php';
        require_once __DIR__ . '/../../views/forum/detail.php';
        require_once __DIR__ . '/../../views/layouts/footer.php';
    }

    // Xử lý Gửi Bình luận (hỗ trợ AJAX)
    public function storeComment() {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $post_id   = (int) ($_POST['post_id']  ?? 0);
            $parent_id = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;
            $content   = trim($_POST['content'] ?? '');

            // Validate nội dung
            if (empty($content)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Nội dung bình luận không được để trống!']);
                    exit();
                }
                $_SESSION['error'] = "Nội dung bình luận không được để trống!";
                if (isset($_GET['admin'])) {
                    header('Location: ?action=admin_post_comments&id=' . $post_id);
                } else {
                    header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
                }
                exit();
            }
            if (mb_strlen($content) > 2000) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Bình luận quá dài (tối đa 2000 ký tự)!']);
                    exit();
                }
                $_SESSION['error'] = "Bình luận quá dài (tối đa 2000 ký tự)!";
                if (isset($_GET['admin'])) {
                    header('Location: ?action=admin_post_comments&id=' . $post_id);
                } else {
                    header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
                }
                exit();
            }

            $commentId = $forumModel->addComment($post_id, $_SESSION['user_id'], $parent_id, $content);

            if ($commentId) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success'    => true,
                        'comment_id' => $commentId,
                        'post_id'    => $post_id,
                        'parent_id'  => $parent_id,
                        'message'    => 'Đã gửi bình luận thành công!',
                    ]);
                    exit();
                }
                $_SESSION['success'] = "Đã gửi bình luận thành công!";
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Gửi bình luận thất bại, vui lòng thử lại!']);
                    exit();
                }
                $_SESSION['error'] = "Gửi bình luận thất bại, vui lòng thử lại!";
            }

            if (isset($_GET['admin'])) {
                header('Location: ?action=admin_post_comments&id=' . $post_id);
            } else {
                header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
            }
            exit();
        }
    }

    // Xử lý Xóa Bình luận
    public function deleteComment() {
        if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $comment_id = (int) $_GET['id'];
            $comment    = $forumModel->getCommentById($comment_id);

            if (!$comment) {
                $_SESSION['error'] = "Bình luận không tồn tại!";
                header('Location: ?action=forum');
                exit();
            }

            $post_id = $comment['post_id'];

            // Kiểm tra quyền: Chủ comment hoặc Admin hoặc Giảng viên
            if ($comment['user_id'] == $_SESSION['user_id'] || in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor'])) {
                $forumModel->deleteComment($comment_id);
                $_SESSION['success'] = "Bình luận đã được xóa!";
            } else {
                $_SESSION['error'] = "Bạn không có quyền xóa bình luận này!";
            }

            if (isset($_GET['admin'])) {
                header('Location: ?action=admin_post_comments&id=' . $post_id);
            } else {
                header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
            }
            exit();
        }
    }

    // Xừ lý Cập nhật Bình luận
    public function updateComment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $comment_id = (int) ($_POST['comment_id'] ?? 0);
            $content    = trim($_POST['content'] ?? '');
            $comment    = $forumModel->getCommentById($comment_id);

            if (!$comment) {
                $_SESSION['error'] = "Bình luận không tồn tại!";
                header('Location: ?action=forum');
                exit();
            }

            $post_id = $comment['post_id'];

            // Kiểm tra quyền: Chủ comment hoặc Admin hoặc Giảng viên
            if ($comment['user_id'] != $_SESSION['user_id'] && !in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor'])) {
                $_SESSION['error'] = "Bạn không có quyền sửa bình luận này!";
                if (isset($_GET['admin'])) {
                    header('Location: ?action=admin_post_comments&id=' . $post_id);
                } else {
                    header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
                }
                exit();
            }

            // Validate nội dung
            if (empty($content)) {
                $_SESSION['error'] = "Nội dung bình luận không được để trống!";
                if (isset($_GET['admin'])) {
                    header('Location: ?action=admin_post_comments&id=' . $post_id);
                } else {
                    header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
                }
                exit();
            }
            if (mb_strlen($content) > 2000) {
                $_SESSION['error'] = "Bình luận quá dài (tối đa 2000 ký tự)!";
                if (isset($_GET['admin'])) {
                    header('Location: ?action=admin_post_comments&id=' . $post_id);
                } else {
                    header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
                }
                exit();
            }

            if ($forumModel->updateComment($comment_id, $content)) {
                $_SESSION['success'] = "Đã cập nhật bình luận!";
            } else {
                $_SESSION['error'] = "Cập nhật thất bại, vui lòng thử lại!";
            }

            if (isset($_GET['admin'])) {
                header('Location: ?action=admin_post_comments&id=' . $post_id);
            } else {
                header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
            }
            exit();
        }
    }

    // Xử lý Ghim / Bỏ ghim Bình luận
    public function pinComment() {
        if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
            // Chỉ Admin hoặc Instructor mới được ghim
            if (!in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor'])) {
                $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này!";
                header('Location: ?action=forum');
                exit();
            }

            $db = (new Database())->getConnection();
            $forumModel = new Forum($db);

            $comment_id = (int) $_GET['id'];
            $comment    = $forumModel->getCommentById($comment_id);

            if (!$comment) {
                $_SESSION['error'] = "Bình luận không tồn tại!";
                header('Location: ?action=forum');
                exit();
            }

            $post_id = $comment['post_id'];

            if ($forumModel->togglePinComment($comment_id)) {
                $_SESSION['success'] = "Đã thay đổi trạng thái ghim bình luận!";
            } else {
                $_SESSION['error'] = "Lỗi khi cập nhật trạng thái ghim!";
            }

            if (isset($_GET['admin'])) {
                header('Location: ?action=admin_post_comments&id=' . $post_id);
            } else {
                header('Location: ?action=forum_detail&id=' . $post_id . '#comments');
            }
            exit();
        }
    }

    // Xử lý Toggle Like / Dislike bình luận (hỗ trợ cả AJAX và redirect thường)
    public function likeComment() {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!isset($_SESSION['user_id'])) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thả cảm xúc!']);
                exit();
            }
            $_SESSION['error'] = "Vui lòng đăng nhập để thả cảm xúc!";
            header('Location: ?action=login');
            exit();
        }

        $comment_id = (int) ($_GET['comment_id'] ?? 0);
        $post_id    = (int) ($_GET['post_id']    ?? 0);
        $type       = in_array($_GET['type'] ?? '', ['like', 'dislike']) ? $_GET['type'] : 'like';
        $csort      = in_array($_GET['csort'] ?? '', ['latest', 'popular']) ? $_GET['csort'] : 'latest';

        if (!$comment_id) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Bình luận không hợp lệ!']);
                exit();
            }
            header('Location: ?action=forum');
            exit();
        }

        $db = (new Database())->getConnection();
        $forumModel = new Forum($db);
        $forumModel->toggleCommentLike($comment_id, $_SESSION['user_id'], $type);

        if ($isAjax) {
            // Lấy số liệu mới nhất sau khi toggle
            $stmt = $db->prepare(
                "SELECT
                    (SELECT COUNT(*) FROM comment_likes WHERE comment_id = :c1 AND type = 'like')    AS like_count,
                    (SELECT COUNT(*) FROM comment_likes WHERE comment_id = :c2 AND type = 'dislike') AS dislike_count,
                    (SELECT cl.type FROM comment_likes cl WHERE cl.comment_id = :c3 AND cl.user_id = :uid LIMIT 1) AS user_reaction"
            );
            $stmt->execute([':c1' => $comment_id, ':c2' => $comment_id, ':c3' => $comment_id, ':uid' => $_SESSION['user_id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode([
                'success'       => true,
                'like_count'    => (int)($data['like_count']    ?? 0),
                'dislike_count' => (int)($data['dislike_count'] ?? 0),
                'user_reaction' => $data['user_reaction'],  // null | 'like' | 'dislike'
            ]);
            exit();
        }

        // Fallback: redirect thường (khi JS bị tắt)
        if (isset($_GET['admin'])) {
            header('Location: ?action=admin_post_comments&id=' . $post_id . '&csort=' . $csort);
        } else {
            header('Location: ?action=forum_detail&id=' . $post_id . '&csort=' . $csort . '#comments');
        }
        exit();
    }
}
?>