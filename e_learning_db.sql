-- =====================================================
-- DATABASE: e_learning_db
-- Được tái tạo từ các Models của dự án E-Learning
-- Tạo ngày: 2026-06-29
-- =====================================================

CREATE DATABASE IF NOT EXISTS `e_learning_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `e_learning_db`;

-- =====================================================
-- BẢNG 1: users
-- Nguồn: User.php
-- Lưu thông tin tài khoản (hỗ trợ đăng nhập Google)
-- =====================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fullname`   VARCHAR(255) NOT NULL,
    `email`      VARCHAR(255) NOT NULL,
    `password`   VARCHAR(255) NULL DEFAULT NULL,
    `google_id`  VARCHAR(255) NULL DEFAULT NULL,
    `avatar`     VARCHAR(500) NULL DEFAULT NULL,
    `role`       ENUM('student','instructor','admin') NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    UNIQUE KEY `uq_users_google_id` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 2: courses
-- Nguồn: Course.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `courses` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(500) NOT NULL,
    `description`  TEXT NULL DEFAULT NULL,
    `thumbnail`    VARCHAR(500) NULL DEFAULT NULL,
    `benefits`     TEXT NULL DEFAULT NULL,
    `requirements` TEXT NULL DEFAULT NULL,
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 3: chapters
-- Nguồn: Curriculum.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `chapters` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `course_id` INT UNSIGNED NOT NULL,
    `title`     VARCHAR(500) NOT NULL,
    `order_num` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_chapters_course_id` (`course_id`),
    CONSTRAINT `fk_chapters_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 4: materials
-- Nguồn: Curriculum.php, Progress.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `materials` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `chapter_id` INT UNSIGNED NOT NULL,
    `title`      VARCHAR(500) NOT NULL,
    `type`       ENUM('video','document','quiz') NOT NULL DEFAULT 'video',
    `content`    TEXT NULL DEFAULT NULL,
    `order_num`  INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_materials_chapter_id` (`chapter_id`),
    CONSTRAINT `fk_materials_chapter`
        FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 5: enrollments
-- Nguồn: Enrollment.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `enrollments` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED NOT NULL,
    `course_id`   INT UNSIGNED NOT NULL,
    `enrolled_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_enrollment` (`user_id`, `course_id`),
    KEY `idx_enrollments_course_id` (`course_id`),
    CONSTRAINT `fk_enrollments_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_enrollments_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 6: material_completions
-- Nguồn: Progress.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `material_completions` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      INT UNSIGNED NOT NULL,
    `material_id`  INT UNSIGNED NOT NULL,
    `completed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_completion` (`user_id`, `material_id`),
    KEY `idx_completions_material_id` (`material_id`),
    CONSTRAINT `fk_completions_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_completions_material`
        FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 7: posts
-- Nguồn: Forum.php
-- =====================================================
CREATE TABLE IF NOT EXISTS `posts` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `title`      VARCHAR(500) NOT NULL,
    `content`    TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_posts_user_id` (`user_id`),
    CONSTRAINT `fk_posts_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG 8: comments
-- Nguồn: Forum.php (hỗ trợ reply lồng nhau qua parent_id)
-- =====================================================
CREATE TABLE IF NOT EXISTS `comments` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `post_id`    INT UNSIGNED NOT NULL,
    `user_id`    INT UNSIGNED NOT NULL,
    `parent_id`  INT UNSIGNED NULL DEFAULT NULL,
    `content`    TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comments_post_id` (`post_id`),
    KEY `idx_comments_user_id` (`user_id`),
    KEY `idx_comments_parent_id` (`parent_id`),
    CONSTRAINT `fk_comments_post`
        FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_comments_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_comments_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DỮ LIỆU MẪU (OPTIONAL)
-- =====================================================

-- Tài khoản Admin mặc định (password: admin123)
INSERT INTO `users` (`fullname`, `email`, `password`, `role`) VALUES
('Admin', 'admin@elearning.com', '\\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Khóa học mẫu
INSERT INTO `courses` (`title`, `description`, `benefits`, `requirements`) VALUES
('Lập trình PHP từ cơ bản đến nâng cao', 'Khóa học toàn diện về PHP cho người mới bắt đầu.', 'Nắm vững PHP, xây dựng được web động', 'Biết HTML/CSS cơ bản'),
('Thiết kế Web với HTML & CSS', 'Học cách xây dựng giao diện web đẹp và responsive.', 'Tự thiết kế được website chuyên nghiệp', 'Không cần kiến thức trước');

-- Chương mẫu cho khóa học 1
INSERT INTO `chapters` (`course_id`, `title`, `order_num`) VALUES
(1, 'Giới thiệu về PHP', 1),
(1, 'Biến và Kiểu dữ liệu', 2),
(1, 'Làm việc với Database', 3);

-- Tài liệu mẫu
INSERT INTO `materials` (`chapter_id`, `title`, `type`, `content`, `order_num`) VALUES
(1, 'PHP là gì?', 'video', 'https://www.youtube.com/watch?v=example1', 1),
(1, 'Cài đặt môi trường XAMPP', 'video', 'https://www.youtube.com/watch?v=example2', 2),
(2, 'Khai báo biến trong PHP', 'document', 'Nội dung bài học...', 1);
