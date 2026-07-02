-- =====================================================
-- SQL PATCH: Nâng cấp bảng comments
-- Ngày: 30/06/2026
-- Mô tả: Thêm cột updated_at để theo dõi bình luận đã chỉnh sửa
-- =====================================================

-- Thêm cột updated_at vào bảng comments (nếu chưa có)
ALTER TABLE `comments`
    ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- =====================================================
-- Tạo bảng comment_likes (Thích / Không thích bình luận)
-- =====================================================
CREATE TABLE IF NOT EXISTS `comment_likes` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `comment_id` INT UNSIGNED NOT NULL,
    `user_id`    INT UNSIGNED NOT NULL,
    `type`       ENUM('like','dislike') NOT NULL DEFAULT 'like',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_comment_like` (`comment_id`, `user_id`),
    KEY `idx_comment_likes_comment` (`comment_id`),
    KEY `idx_comment_likes_user` (`user_id`),
    CONSTRAINT `fk_comment_likes_comment`
        FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_comment_likes_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- HƯỚNG DẪN CHẠY:
-- Vào phpMyAdmin → chọn database e_learning_db → SQL → paste và chạy đoạn trên
-- =====================================================
