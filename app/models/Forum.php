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
    public function getAllPosts($search = '', $sort = 'latest', $author_id = null) {
        // THÊM: u.avatar as author_avatar
        $query = "SELECT p.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role,
                 (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
                 FROM posts p 
                 JOIN users u ON p.user_id = u.id WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND (p.title LIKE :search_title OR p.content LIKE :search_content)";
        }
        if (!empty($author_id)) {
            $query .= " AND p.user_id = :author_id";
        }
        
        if ($sort === 'popular') {
            $query .= " ORDER BY comment_count DESC, p.created_at DESC";
        } elseif ($sort === 'oldest') {
            $query .= " ORDER BY p.created_at ASC"; 
        } else {
            $query .= " ORDER BY p.created_at DESC"; 
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) { 
            $stmt->bindValue(':search_title', '%' . $search . '%'); 
            $stmt->bindValue(':search_content', '%' . $search . '%'); 
        }
        if (!empty($author_id)) {
            $stmt->bindValue(':author_id', $author_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    // Lấy toàn bộ bình luận của 1 bài (Đã bỏ avatar, dùng fullname)
    public function getComments($post_id) {
        // THÊM: u.avatar as author_avatar
        $query = "SELECT c.*, u.fullname as author_name, u.avatar as author_avatar, u.role as author_role,
                         parent_u.fullname as parent_author_name 
                  FROM comments c 
                  JOIN users u ON c.user_id = u.id 
                  LEFT JOIN comments parent_c ON c.parent_id = parent_c.id 
                  LEFT JOIN users parent_u ON parent_c.user_id = parent_u.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đăng bình luận mới (Có hỗ trợ parent_id nếu là Reply)
    public function addComment($post_id, $user_id, $parent_id, $content) {
        $query = "INSERT INTO comments (post_id, user_id, parent_id, content) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$post_id, $user_id, $parent_id, $content]);
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
}
?>