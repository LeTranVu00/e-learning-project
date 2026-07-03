-- Tạo bảng categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(100) NOT NULL DEFAULT 'fa-folder',
    `color` VARCHAR(100) NOT NULL DEFAULT 'bg-gray-100 text-gray-600',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chèn dữ liệu mẫu
INSERT INTO `categories` (`name`, `icon`, `color`) VALUES
('Lập trình Web', 'fa-laptop-code', 'bg-blue-100 text-blue-600'),
('Lập trình Mobile', 'fa-mobile-screen', 'bg-green-100 text-green-600'),
('Data Science', 'fa-chart-pie', 'bg-purple-100 text-purple-600'),
('UI/UX Design', 'fa-pen-nib', 'bg-pink-100 text-pink-600'),
('Khởi nghiệp', 'fa-lightbulb', 'bg-yellow-100 text-yellow-600'),
('Trí tuệ nhân tạo (AI)', 'fa-robot', 'bg-indigo-100 text-indigo-600');

-- Thêm cột category_id vào bảng courses
ALTER TABLE `courses` ADD COLUMN `category_id` INT UNSIGNED NULL DEFAULT NULL AFTER `id`;

-- Gắn khóa ngoại
ALTER TABLE `courses` 
ADD CONSTRAINT `fk_courses_category` 
FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Cập nhật mẫu (Set category_id = 1 cho các khóa hiện có)
UPDATE `courses` SET `category_id` = 1 WHERE `category_id` IS NULL;
