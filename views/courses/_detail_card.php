<?php
// File: views/courses/_detail_card.php
// Biến có sẵn từ detail.php: $course, $price, $original_price, $instructor,
// $level, $duration_hours, $total_lessons, $language, $is_enrolled, $total_materials_count

$start_date    = !empty($course['start_date'])    ? date('d/m/Y', strtotime($course['start_date'])) : null;
$schedule      = $course['schedule']      ?? null;
$study_time    = $course['study_time']    ?? null;
$contact_phone = $course['contact_phone'] ?? null;
// Số bài tự tính từ curriculum (không dùng total_lessons thủ công nữa)
$lessons_count = $total_materials_count;

// Tách lịch học thành mảng để render dạng pills
$schedule_items = [];
if ($schedule) {
    $schedule_items = array_map('trim', explode(',', $schedule));
}
?>

<style>
/* =====================
   SIDEBAR CARD — 28Tech style
   ===================== */
.sc-card {
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(80, 80, 160, 0.10);
    transform: translateZ(0);
}
.dark .sc-card { background: #1f2937; border-color: #374151; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); }

/* Thumbnail */
.sc-thumb {
    position: relative;
    overflow: hidden;
    background: #1e1b4b;
}
.sc-thumb img {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}
.sc-thumb:hover img { transform: scale(1.03); }

/* Body */
.sc-body { padding: 20px 22px; }

/* Price block */
.sc-price-current {
    font-size: 1.8rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.5px;
}
.dark .sc-price-current { color: #f9fafb; }
.sc-price-current.free { color: #059669; }
.dark .sc-price-current.free { color: #10b981; }
.sc-price-old {
    font-size: 1rem;
    color: #9ca3af;
    text-decoration: line-through;
    margin-left: 8px;
}

/* CTA Button — blue gradient giống 28tech */
.sc-btn {
    display: block;
    width: 100%;
    padding: 14px 20px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    text-align: center;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.2s;
    letter-spacing: 0.3px;
}
.sc-btn-pay {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #0ea5e9 100%);
    color: #fff;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.sc-btn-pay:hover {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #0284c7 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(37,99,235,0.45);
}
.sc-btn-free {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #0ea5e9 100%);
    color: #fff;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.sc-btn-free:hover { background: linear-gradient(135deg, #1e3a8a, #1d4ed8); transform: translateY(-1px); }
.sc-btn-continue {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    box-shadow: 0 4px 16px rgba(245,158,11,0.35);
}
.sc-btn-continue:hover { transform: translateY(-1px); }
.sc-btn-login {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #0ea5e9 100%);
    color: #fff;
}

/* Meta rows */
.sc-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 11px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
    gap: 8px;
}
.dark .sc-row { border-color: #374151; }
.sc-row:last-child { border-bottom: none; }
.sc-row-label { color: #6b7280; white-space: nowrap; flex-shrink: 0; }
.dark .sc-row-label { color: #9ca3af; }
.sc-row-value { font-weight: 600; color: #111827; text-align: right; }
.dark .sc-row-value { color: #f3f4f6; }
.sc-row-value.blue { color: #2563eb; }
.dark .sc-row-value.blue { color: #60a5fa; }

/* Schedule pills */
.sc-schedule-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    justify-content: flex-end;
}
.sc-pill {
    display: inline-block;
    padding: 3px 8px;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
}
.dark .sc-pill { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }

/* Contact box */
.sc-contact {
    background: #f8f9ff;
    border-radius: 12px;
    padding: 14px 16px;
    margin-top: 16px;
    text-align: center;
    border: 1px solid #e8eaf6;
}
.dark .sc-contact { background: #111827; border-color: #1f2937; }
.sc-contact-label {
    font-size: 0.82rem;
    color: #6b7280;
    margin-bottom: 10px;
}
.dark .sc-contact-label { color: #9ca3af; }
.sc-zalo-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ede9fe;
    color: #4f46e5;
    border: none;
    border-radius: 24px;
    padding: 8px 20px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
    width: 100%;
    justify-content: center;
}
.sc-zalo-btn:hover { background: #ddd6fe; transform: translateY(-1px); }
</style>

<div class="sc-card">

    <!-- Thumbnail -->
    <div class="sc-thumb">
        <?php
        $thumb = !empty($course['thumbnail']) ? $course['thumbnail'] : null;
        $thumb_url = $thumb
            ? (str_starts_with($thumb, 'http') ? $thumb : '/e-learning-project/public/' . $thumb)
            : 'https://placehold.co/640x360/1e1b4b/ffffff?text=' . urlencode($course['title']);
        ?>
        <img
            src="<?= htmlspecialchars($thumb_url) ?>"
            alt="<?= htmlspecialchars($course['title']) ?>"
            onerror="this.src='https://placehold.co/640x360/1e1b4b/ffffff?text=Khóa+Học'"
        >
    </div>

    <div class="sc-body">

        <!-- Price -->
        <div class="flex items-baseline flex-wrap gap-1 mb-4">
            <?php if ($price > 0): ?>
                <span class="sc-price-current"><?= number_format($price, 0, ',', '.') ?> đ</span>
                <?php if ($original_price > $price): ?>
                    <span class="sc-price-old"><?= number_format($original_price, 0, ',', '.') ?> đ</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="sc-price-current free">Miễn phí</span>
            <?php endif; ?>
        </div>

        <!-- Meta rows -->
        <div class="mt-5">

            <?php if ($start_date): ?>
            <div class="sc-row">
                <span class="sc-row-label">Ngày khai giảng</span>
                <span class="sc-row-value blue"><?= htmlspecialchars($start_date) ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($schedule_items)): ?>
            <div class="sc-row">
                <span class="sc-row-label">Lịch học</span>
                <div class="sc-schedule-pills">
                    <?php foreach ($schedule_items as $s): ?>
                        <span class="sc-pill"><?= htmlspecialchars($s) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($study_time): ?>
            <div class="sc-row">
                <span class="sc-row-label">Giờ học</span>
                <span class="sc-row-value"><?= htmlspecialchars($study_time) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($lessons_count > 0): ?>
            <div class="sc-row">
                <span class="sc-row-label">Số lượng bài giảng</span>
                <span class="sc-row-value"><?= $lessons_count ?></span>
            </div>
            <?php endif; ?>

            <?php if ($instructor): ?>
            <div class="sc-row">
                <span class="sc-row-label">Giảng viên</span>
                <span class="sc-row-value blue"><?= htmlspecialchars($instructor) ?></span>
            </div>
            <?php endif; ?>

        </div>

        <!-- Call to Action -->
        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
            <?php if ($is_enrolled): ?>
                <a href="?action=learn&id=<?= $course['id'] ?>" class="sc-btn sc-btn-continue">
                    <i class="fa-solid fa-play-circle mr-2"></i>Tiếp tục học
                </a>
            <?php elseif ($price > 0): ?>
                <div class="flex flex-col gap-3">
                    <a href="?action=pay&id=<?= $course['id'] ?>" class="sc-btn sc-btn-pay">
                        Mua ngay
                    </a>
                    <?php $in_cart = isset($_SESSION['cart']) && in_array($course['id'], $_SESSION['cart']); ?>
                    <button data-type="detail" data-in-cart="<?= $in_cart ? 'true' : 'false' ?>" onclick="toggleCart(<?= $course['id'] ?>, this)" class="sc-btn hover:shadow-md hover:-translate-y-0.5" style="<?= $in_cart ? 'background: #eff6ff; border: 2px solid #3b82f6; color: #1d4ed8; transition: all 0.2s;' : 'background: white; border: 2px solid #2563eb; color: #2563eb; box-shadow: 0 2px 8px rgba(37,99,235,0.15); transition: all 0.2s;' ?>">
                        <i class="fa-solid <?= $in_cart ? 'fa-cart-arrow-down mr-2 text-blue-600' : 'fa-cart-plus mr-2' ?>"></i><?= $in_cart ? 'Xóa khỏi giỏ hàng' : 'Thêm vào giỏ hàng' ?>
                    </button>
                </div>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <a href="?action=enroll_course&id=<?= $course['id'] ?>" class="sc-btn sc-btn-free">
                    Đăng Ký Học
                </a>
            <?php else: ?>
                <a href="?action=login" class="sc-btn sc-btn-login">
                    Đăng nhập để đăng ký
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>
