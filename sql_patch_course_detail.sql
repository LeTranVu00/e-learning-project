-- =====================================================
-- PATCH: Thêm các cột mới vào bảng courses
-- Phục vụ trang Chi tiết Khóa học (detail.php)
-- Chạy file này trên phpMyAdmin hoặc MySQL CLI
-- =====================================================

USE `e_learning_db`;

-- Thêm các cột mới (dùng IF NOT EXISTS để tránh lỗi nếu đã chạy rồi)
ALTER TABLE `courses`
    ADD COLUMN IF NOT EXISTS `price`          INT           NOT NULL DEFAULT 0         AFTER `requirements`,
    ADD COLUMN IF NOT EXISTS `original_price` INT           NOT NULL DEFAULT 0         AFTER `price`,
    ADD COLUMN IF NOT EXISTS `instructor`     VARCHAR(255)  NULL DEFAULT NULL           AFTER `original_price`,
    ADD COLUMN IF NOT EXISTS `level`          VARCHAR(50)   NULL DEFAULT 'Sơ cấp'      AFTER `instructor`,
    ADD COLUMN IF NOT EXISTS `duration_hours` INT           NOT NULL DEFAULT 0         AFTER `level`,
    ADD COLUMN IF NOT EXISTS `total_lessons`  INT           NOT NULL DEFAULT 0         AFTER `duration_hours`,
    ADD COLUMN IF NOT EXISTS `language`       VARCHAR(50)   NOT NULL DEFAULT 'Tiếng Việt' AFTER `total_lessons`,
    ADD COLUMN IF NOT EXISTS `start_date`     DATE          NULL DEFAULT NULL           AFTER `language`,
    ADD COLUMN IF NOT EXISTS `schedule`       VARCHAR(255)  NULL DEFAULT NULL           AFTER `start_date`,
    ADD COLUMN IF NOT EXISTS `study_time`     VARCHAR(100)  NULL DEFAULT NULL           AFTER `schedule`,
    ADD COLUMN IF NOT EXISTS `contact_phone`  VARCHAR(50)   NULL DEFAULT NULL           AFTER `study_time`;

-- =====================================================
-- Cập nhật dữ liệu mẫu đầy đủ cho khóa học 1
-- =====================================================
UPDATE `courses` SET
    `price`          = 499000,
    `original_price` = 999000,
    `instructor`     = 'Nguyễn Văn Tuấn',
    `level`          = 'Sơ cấp',
    `duration_hours` = 40,
    `total_lessons`  = 120,
    `language`       = 'Tiếng Việt',
    `benefits`       = '<ul><li>Nắm vững nền tảng PHP từ cơ bản đến nâng cao</li><li>Xây dựng được ứng dụng web động hoàn chỉnh</li><li>Hiểu rõ mô hình MVC trong thực tế</li><li>Làm việc với MySQL, PDO an toàn và hiệu quả</li><li>Triển khai dự án thực tế lên server hosting</li><li>Nhận chứng chỉ hoàn thành khóa học</li></ul>',
    `requirements`   = '<ul><li>Biết HTML/CSS cơ bản</li><li>Có máy tính cài đặt được XAMPP hoặc Laragon</li><li>Không cần biết PHP trước, sẽ học từ đầu</li></ul>',
    `description`    = '<p>Khóa học <strong>Lập trình PHP từ cơ bản đến nâng cao</strong> là khóa học toàn diện giúp bạn nắm vững ngôn ngữ lập trình phía máy chủ phổ biến nhất thế giới.</p><p>Trong khóa học này, bạn sẽ được học từ những khái niệm cơ bản nhất của PHP, sau đó tiến đến các chủ đề nâng cao như OOP, MVC, bảo mật web và tích hợp thanh toán trực tuyến.</p>'
WHERE id = 1;

UPDATE `courses` SET
    `price`          = 0,
    `original_price` = 0,
    `instructor`     = 'Lê Thị Hương',
    `level`          = 'Sơ cấp',
    `duration_hours` = 25,
    `total_lessons`  = 80,
    `language`       = 'Tiếng Việt',
    `benefits`       = '<ul><li>Tự thiết kế được website chuyên nghiệp</li><li>Hiểu sâu về CSS Flexbox và Grid</li><li>Làm chủ kỹ thuật Responsive Design</li><li>Tối ưu giao diện cho mọi thiết bị</li></ul>',
    `requirements`   = '<ul><li>Không cần kiến thức trước, học từ số 0</li><li>Chỉ cần có máy tính và trình duyệt web</li></ul>',
    `description`    = '<p>Khóa học <strong>Thiết kế Web với HTML &amp; CSS</strong> sẽ đưa bạn từ người hoàn toàn mới bắt đầu đến khi tự mình thiết kế được các website đẹp và chuyên nghiệp.</p>'
WHERE id = 2;
