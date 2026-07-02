-- Thêm cột is_featured vào bảng courses
ALTER TABLE `courses`
    ADD COLUMN IF NOT EXISTS `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `title`;

-- =====================================================
-- HƯỚNG DẪN CHẠY:
-- Vào phpMyAdmin → chọn database e_learning_db → SQL → paste và chạy đoạn trên
-- =====================================================
