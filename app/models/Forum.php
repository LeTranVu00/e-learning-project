<?php
// File: app/models/Forum.php

class Forum {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả bài viết để hiển thị ngoài trang chủ Forum
    // Lấy tất cả bài viết có hỗ trợ Tìm kiếm & Sắp xếp
    // Lấy tất cả bài viết có hỗ trợ Tìm kiếm Tiêu đề & Nội dung
    // Lấy tất cả bài viết có hỗ trợ Tìm kiếm Tiêu đề & Nội dung
    // Lấy tất cả bài viết (Đã thêm bộ lọc theo ID Tác giả)
    public function getAllPosts($search = '', $sort = 'latest', $author_id = null, $date = '', $limit = null, $offset = null) {
        // THÊM: u.avatar as author_avatar
        $query = "SELECT p.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role,
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
                 FROM posts p 
                 JOIN users u ON p.user_id = u.id WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (p.title LIKE :search_title OR p.content LIKE :search_content OR u.fullname LIKE :search_author)";
        }
        if (!empty($author_id)) {
            $query .= " AND p.user_id = :author_id";
        }
        if (!empty($date)) {
            $query .= " AND DATE(p.created_at) = :filter_date";
        }
        
        if ($sort === 'popular') {
            $query .= " ORDER BY comment_count DESC, p.created_at DESC";
        } elseif ($sort === 'oldest') {
            $query .= " ORDER BY p.created_at ASC"; 
        } else {
            $query .= " ORDER BY p.created_at DESC"; 
        }

        if ($limit !== null && $offset !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) { 
            $stmt->bindValue(':search_title', '%' . $search . '%'); 
            $stmt->bindValue(':search_content', '%' . $search . '%'); 
            $stmt->bindValue(':search_author', '%' . $search . '%'); 
        }
        if (!empty($author_id)) {
            $stmt->bindValue(':author_id', $author_id);
        }
        if (!empty($date)) {
            $stmt->bindValue(':filter_date', $date);
        }
        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số bài viết
    public function getTotalPostsCount($search = '', $author_id = null, $date = '') {
        $query = "SELECT COUNT(*) FROM posts p JOIN users u ON p.user_id = u.id WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (p.title LIKE :search_title OR p.content LIKE :search_content OR u.fullname LIKE :search_author)";
        }
        if (!empty($author_id)) {
            $query .= " AND p.user_id = :author_id";
        }
        if (!empty($date)) {
            $query .= " AND DATE(p.created_at) = :filter_date";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) { 
            $stmt->bindValue(':search_title', '%' . $search . '%'); 
            $stmt->bindValue(':search_content', '%' . $search . '%'); 
            $stmt->bindValue(':search_author', '%' . $search . '%'); 
        }
        if (!empty($author_id)) {
            $stmt->bindValue(':author_id', $author_id);
        }
        if (!empty($date)) {
            $stmt->bindValue(':filter_date', $date);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Tạo bài viết mới
    public function createPost($user_id, $title, $content) {
        $query = "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id, $title, $content]);
    }

    // Lấy thông tin 1 bài viết cụ thể
    // Lấy thông tin 1 bài viết cụ thể (chi tiết kèm thông tin tác giả)
    // Note: detailed implementation below; keep single definition to avoid redeclare error

    // Cập nhật bài viết
    public function updatePost($id, $title, $content) {
        $query = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $content, $id]);
    }

    // Xóa bài viết
    public function deletePost($id) {
        $query = "DELETE FROM posts WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }


    // Lấy chi tiết 1 bài viết kèm thông tin tác giả
    // Lấy chi tiết 1 bài viết kèm thông tin tác giả (Đã bỏ avatar, dùng fullname)
    public function getPostById($id) {
        // THÊM: u.avatar as author_avatar
        $query = "SELECT p.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role 
                  FROM posts p 
                  JOIN users u ON p.user_id = u.id 
                  WHERE p.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Bật/tắt trạng thái nổi bật của bài viết
    public function toggleFeaturedPost($post_id) {
        // Lấy trạng thái hiện tại
        $query = "SELECT is_featured FROM posts WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$post_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $new_status = $result['is_featured'] ? 0 : 1;
            $update = "UPDATE posts SET is_featured = ? WHERE id = ?";
            $updateStmt = $this->conn->prepare($update);
            return $updateStmt->execute([$new_status, $post_id]);
        }
        return false;
    }

    // Lấy danh sách các bài viết nổi bật
    public function getFeaturedPosts($limit = 3) {
        $query = "SELECT p.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role,
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
                 FROM posts p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE p.is_featured = 1 
                 ORDER BY p.created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy toàn bộ bình luận kèm like/dislike count và reaction của user hiện tại
    public function getComments($post_id, $user_id = null, $sort = 'latest') {
        $userReactionSQL = $user_id 
            ? ", (SELECT cl.type FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = :user_id LIMIT 1) as user_reaction"
            : ", NULL as user_reaction";

        $orderSQL = ($sort === 'popular')
            ? "ORDER BY c.is_pinned DESC, like_count DESC, c.created_at DESC"
            : "ORDER BY c.is_pinned DESC, c.created_at DESC";

        $query = "SELECT c.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role,
                         parent_u.fullname as parent_author_name,
                         (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND type = 'like')    as like_count,
                         (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND type = 'dislike') as dislike_count
                         {$userReactionSQL}
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  LEFT JOIN comments parent_c ON c.parent_id = parent_c.id
                  LEFT JOIN users parent_u ON parent_c.user_id = parent_u.id
                  WHERE c.post_id = :post_id
                  {$orderSQL}";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        if ($user_id) {
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Toggle Like / Dislike cho bình luận
    // Logic: Chưa react → thêm | Click cùng loại → xóa (bỏ react) | Click khác loại → đổi
    public function toggleCommentLike($comment_id, $user_id, $type) {
        $stmt = $this->conn->prepare("SELECT id, type FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $stmt->execute([$comment_id, $user_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if ($existing['type'] === $type) {
                // Cùng loại → bỏ react
                $stmt = $this->conn->prepare("DELETE FROM comment_likes WHERE id = ?");
                return $stmt->execute([$existing['id']]);
            } else {
                // Khác loại → đổi
                $stmt = $this->conn->prepare("UPDATE comment_likes SET type = ? WHERE id = ?");
                return $stmt->execute([$type, $existing['id']]);
            }
        } else {
            // Chưa react → thêm mới
            $stmt = $this->conn->prepare("INSERT INTO comment_likes (comment_id, user_id, type) VALUES (?, ?, ?)");
            return $stmt->execute([$comment_id, $user_id, $type]);
        }
    }

    // Đăng bình luận mới (Có hỗ trợ parent_id nếu là Reply)
    public function addComment($post_id, $user_id, $parent_id, $content) {
        $query = "INSERT INTO comments (post_id, user_id, parent_id, content) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute([$post_id, $user_id, $parent_id, $content])) {
            return (int) $this->conn->lastInsertId();
        }
        return false;
    }

    // Lấy thông tin 1 bình luận (để kiểm tra quyền sửa/xóa)
    public function getCommentById($id) {
        $query = "SELECT c.*, u.fullname as author_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Xóa bình luận và toàn bộ replies con của nó (đệ quy)
    public function deleteComment($id) {
        // Tìm tất cả replies trực tiếp
        $stmt = $this->conn->prepare("SELECT id FROM comments WHERE parent_id = ?");
        $stmt->execute([$id]);
        $children = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đệ quy xóa replies con trước
        foreach ($children as $child) {
            $this->deleteComment($child['id']);
        }

        // Xóa comment này
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Cập nhật nội dung bình luận (ghi lại updated_at)
    public function updateComment($id, $content) {
        $query = "UPDATE comments SET content = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$content, $id]);
    }

    // Ghim / Bỏ ghim bình luận
    public function togglePinComment($id) {
        // Lấy trạng thái hiện tại
        $stmt = $this->conn->prepare("SELECT is_pinned FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($comment) {
            $newStatus = $comment['is_pinned'] ? 0 : 1;
            $updateStmt = $this->conn->prepare("UPDATE comments SET is_pinned = ? WHERE id = ?");
            return $updateStmt->execute([$newStatus, $id]);
        }
        return false;
    }
    

    // Lấy Top bài viết nổi bật (Nhiều bình luận nhất) cho Trang chủ
    public function getTopPosts($limit = 3) {
        $query = "SELECT p.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role,
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
                 FROM posts p 
                 JOIN users u ON p.user_id = u.id 
                 ORDER BY comment_count DESC, p.created_at DESC 
                 LIMIT :limit";
                 
        $stmt = $this->conn->prepare($query);
        // Bắt buộc dùng PDO::PARAM_INT khi bind giá trị cho LIMIT
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả bình luận cho Admin Dashboard
    public function getAllCommentsForAdmin() {
        $query = "SELECT c.*, 
                         u.fullname as author_name, 
                         p.title as post_title,
                         (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND type = 'like') as like_count,
                         (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND type = 'dislike') as dislike_count
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  JOIN posts p ON c.post_id = p.id
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>