-- =====================================================
-- SQL PATCH: Thêm chức năng ghim bình luận
-- Ngày: 02/07/2026
-- =====================================================

ALTER TABLE `comments` ADD COLUMN IF NOT EXISTS `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `parent_id`;
