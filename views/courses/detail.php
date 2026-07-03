<?php
// File: views/courses/detail.php
// Biến có sẵn: $course (array), $curriculum (array of chapters with materials)

$is_enrolled   = false;
$total_chapters = count($curriculum ?? []);
$total_materials_count = 0;
if (!empty($curriculum)) {
    foreach ($curriculum as $c) {
        $total_materials_count += count($c['materials']);
    }
}

// Kiểm tra user đã ghi danh chưa (nếu đã login)
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../app/config/Database.php';
    require_once __DIR__ . '/../../app/models/Enrollment.php';
    $db_check    = (new Database())->getConnection();
    $enrollCheck = new Enrollment($db_check);
    $is_enrolled = $enrollCheck->isEnrolled($_SESSION['user_id'], $course['id']);
}

$price          = $course['price']          ?? 0;
$original_price = $course['original_price'] ?? 0;
$instructor     = $course['instructor']     ?? 'Đội ngũ giảng viên';
$level          = $course['level']          ?? 'Sơ cấp';
$duration_hours = $course['duration_hours'] ?? 0;
$total_lessons  = $course['total_lessons']  ?? $total_materials_count;
$language       = $course['language']       ?? 'Tiếng Việt';
// Fields mới cho card sidebar
$start_date     = $course['start_date']     ?? null;
$schedule       = $course['schedule']       ?? null;
$study_time     = $course['study_time']     ?? null;
$contact_phone  = $course['contact_phone']  ?? null;

?>

<style>
/* ========================
   COURSE DETAIL — CUSTOM CSS
   ======================== */

/* Hero gradient */
.course-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #1e3a5f 100%);
    position: relative;
    /* Full width trick */
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    /* Optional: padding at bottom so the card can overlap nicely */
}
.course-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 70% 50%, rgba(139, 92, 246, 0.25) 0%, transparent 65%),
                radial-gradient(ellipse at 20% 80%, rgba(59, 130, 246, 0.2) 0%, transparent 50%);
    pointer-events: none;
}

/* Tab bar */
.detail-tabs {
    position: sticky;
    top: 0;
    z-index: 40;
    background: #fff;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 2rem;
}
.tab-btn {
    padding: 14px 24px;
    font-weight: 600;
    font-size: 0.9rem;
    color: #6b7280;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.2s;
    background: none;
    border-top: none;
    border-left: none;
    border-right: none;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.tab-btn:hover { color: #4f46e5; }
.tab-btn.active {
    color: #4f46e5;
    border-bottom-color: #4f46e5;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* Sticky sidebar card */
.sticky-card {
    position: -webkit-sticky; /* Hỗ trợ trình duyệt Safari */
    position: sticky;
    top: 100px; /* TĂNG KHOẢNG CÁCH NÀY LÊN. Nếu vẫn bị đè, hãy tăng lên 120px hoặc 150px */
    z-index: 50;
    height: fit-content;
}

/* Benefits list */
.benefits-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 6px 0;
    color: #374151;
    font-size: 0.95rem;
    line-height: 1.5;
}
.benefits-list li::before {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: #10b981;
    font-size: 0.8rem;
    margin-top: 3px;
    flex-shrink: 0;
}
.requirements-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 6px 0;
    color: #374151;
    font-size: 0.95rem;
}
.requirements-list li::before {
    content: '\f111';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: #6366f1;
    font-size: 0.45rem;
    margin-top: 7px;
    flex-shrink: 0;
}

/* Accordion chapter */
.chapter-accordion {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 10px;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s;
}
.chapter-accordion:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.chapter-header {
    width: 100%;
    text-align: left;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fafb;
    cursor: pointer;
    border: none;
    transition: background 0.15s;
}
.chapter-header:hover { background: #f3f4f6; }
.chapter-header .chevron {
    transition: transform 0.3s;
    color: #9ca3af;
    font-size: 0.8rem;
}
.chapter-accordion.open .chapter-header .chevron { transform: rotate(180deg); }
.chapter-body {
    display: none;
    border-top: 1px solid #f3f4f6;
}
.chapter-accordion.open .chapter-body { display: block; }
.material-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    border-bottom: 1px solid #f9fafb;
    transition: background 0.15s;
}
.material-row:last-child { border-bottom: none; }
.material-row:hover { background: #fafafa; }

/* Info badge */
.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
}

/* Sidebar card */
.sidebar-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.1);
}
.sidebar-card .thumbnail-wrap {
    position: relative;
    overflow: hidden;
}
.sidebar-card .thumbnail-wrap img {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
    display: block;
}
.price-tag {
    font-size: 1.75rem;
    font-weight: 800;
    color: #dc2626;
}
.price-original {
    font-size: 1rem;
    color: #9ca3af;
    text-decoration: line-through;
    margin-left: 8px;
}
.enroll-btn {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    text-align: center;
    display: block;
    text-decoration: none;
}
.enroll-btn.primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}
.enroll-btn.primary:hover {
    background: linear-gradient(135deg, #4f46e5, #4338ca);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.5);
}
.enroll-btn.free {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}
.enroll-btn.free:hover { background: linear-gradient(135deg, #059669, #047857); transform: translateY(-1px); }
.enroll-btn.enrolled {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}
.enroll-btn.enrolled:hover { background: linear-gradient(135deg, #d97706, #b45309); transform: translateY(-1px); }

.meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
}
.meta-row:last-child { border-bottom: none; }
.meta-row .label { color: #6b7280; }
.meta-row .value { font-weight: 600; color: #111827; text-align: right; max-width: 60%; }

/* Floating play button on thumbnail */
.play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.25);
    opacity: 0;
    transition: opacity 0.2s;
}
.thumbnail-wrap:hover .play-overlay { opacity: 1; }
.play-circle {
    width: 56px; height: 56px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: #4f46e5;
}

/* Section heading */
.section-heading {
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
}

/* Discount badge */
.discount-badge {
    background: #fef3c7;
    color: #92400e;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
}
</style>

<!-- ============================================================
     HERO SECTION
============================================================ -->
<div class="course-hero px-4 sm:px-6 lg:px-8 py-10 lg:pt-14 lg:pb-40">
    <div class="max-w-7xl mx-auto relative z-10">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-indigo-300 mb-6">
            <a href="?action=home" class="hover:text-white transition">Trang chủ</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="hover:text-white cursor-pointer transition">Khóa học</span>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-white font-medium"><?= htmlspecialchars(mb_strimwidth($course['title'], 0, 50, '...')) ?></span>
        </nav>

        <div class="lg:flex lg:gap-12 lg:items-start">
            <!-- Left: Course info (takes up 2/3 on large screens) -->
            <div class="lg:w-2/3">
                <!-- Title -->
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight mb-4">
                    <?= htmlspecialchars($course['title']) ?>
                </h1>

                <!-- Short description -->
                <p class="text-indigo-200 text-base lg:text-lg mb-6 leading-relaxed max-w-2xl">
                    <?= mb_strimwidth(strip_tags($course['description'] ?? 'Khóa học chất lượng cao cho người muốn nâng cao kỹ năng lập trình.'), 0, 200, '...') ?>
                </p>

                <!-- Badges -->
                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <span class="info-badge" style="background:rgba(245,158,11,0.2); color:#fbbf24; border: 1px solid rgba(245,158,11,0.3);">
                        <i class="fa-solid fa-trophy"></i> Bestseller
                    </span>
                    <div class="flex items-center gap-1 text-yellow-400">
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star-half-stroke text-sm"></i>
                        <span class="text-white font-semibold ml-1">4.8</span>
                        <span class="text-indigo-300 text-sm">(<?= number_format($total_materials_count * 47 + 258) ?> đánh giá)</span>
                    </div>
                    <span class="text-indigo-300 text-sm">
                        <i class="fa-solid fa-users mr-1"></i>
                        <?= number_format($total_materials_count * 31 + 1234) ?> học viên
                    </span>
                </div>

                <!-- Instructor -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        <?= mb_strtoupper(mb_substr($instructor, 0, 1)) ?>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-300">Giảng viên</p>
                        <p class="text-white font-semibold text-sm"><?= htmlspecialchars($instructor) ?></p>
                    </div>
                </div>
            </div>

            <!-- Right: on large screens the sticky card floats here; on mobile it shows below content -->
            <!-- (Card is rendered outside hero, in the main layout below) -->
        </div>
    </div>
</div>

<!-- ============================================================
     MAIN CONTENT + SIDEBAR
============================================================ -->
<div class="flex flex-col lg:flex-row gap-8 mb-20 max-w-7xl mx-auto relative lg:-mt-72 z-20">

    <!-- ===== LEFT CONTENT AREA ===== -->
    <div class="lg:w-2/3 min-w-0 lg:mt-72">

        <!-- Mobile: Show sidebar card ABOVE tabs (only on mobile) -->
        <div class="lg:hidden mb-8">
            <?php include __DIR__ . '/../../views/courses/_detail_card.php'; ?>
        </div>

        <!-- ============================================================
             TAB NAVIGATION BAR
        ============================================================ -->
        <div class="detail-tabs">
            <div class="flex overflow-x-auto hide-scrollbar">
                <button class="tab-btn active" onclick="switchTab('overview', this)" id="tab-overview">
                    <i class="fa-solid fa-book-open"></i> Tổng quan
                </button>
                <button class="tab-btn" onclick="switchTab('curriculum', this)" id="tab-curriculum">
                    <i class="fa-solid fa-list-ul"></i> Lộ trình
                    <span class="ml-1 bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= $total_chapters ?></span>
                </button>
                <button class="tab-btn" onclick="switchTab('detail', this)" id="tab-detail">
                    <i class="fa-solid fa-circle-info"></i> Chi tiết
                </button>
            </div>
        </div>

        <!-- TAB: Tổng quan -->
        <div id="panel-overview" class="tab-panel active">
            <!-- What you'll learn -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-2xl p-6 sm:p-8 mb-8">
                <h2 class="section-heading" style="border-color: #c7d2fe;">
                    <i class="fa-solid fa-graduation-cap text-indigo-500 mr-2"></i>Bạn sẽ học được gì?
                </h2>
                <?php $benefits_html = $course['benefits'] ?? ''; ?>
                <?php if ($benefits_html): ?>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        <?= $benefits_html ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400 italic">Thông tin đang được cập nhật...</p>
                <?php endif; ?>
            </div>

            <!-- Stats strip -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
                    <div class="text-2xl font-extrabold text-indigo-600 mb-1"><?= $total_chapters ?></div>
                    <div class="text-xs text-gray-500">Chương học</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
                    <div class="text-2xl font-extrabold text-indigo-600 mb-1"><?= $total_materials_count ?></div>
                    <div class="text-xs text-gray-500">Bài giảng</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 text-center shadow-sm">
                    <div class="text-2xl font-extrabold text-indigo-600 mb-1"><?= $duration_hours > 0 ? $duration_hours : '—' ?>h</div>
                    <div class="text-xs text-gray-500">Giờ học</div>
                </div>
                <!-- Đã bỏ ô Truy cập mãi mãi theo yêu cầu -->
            </div>


            <!-- Description -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h2 class="section-heading">
                    <i class="fa-solid fa-file-lines text-gray-400 mr-2"></i>Mô tả khóa học
                </h2>
                <div class="text-gray-600 leading-relaxed prose max-w-none">
                    <?= $course['description'] ?? '<p class="italic text-gray-400">Chưa có mô tả.</p>' ?>
                </div>
            </div>
        </div>

        <!-- TAB: Lộ trình -->
        <div id="panel-curriculum" class="tab-panel">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="section-heading mb-0" style="border:none; padding:0;">
                        <i class="fa-solid fa-map-pin text-indigo-500 mr-2"></i>Lộ trình khóa học
                    </h2>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        <?= $total_chapters ?> chương • <?= $total_materials_count ?> bài
                    </span>
                </div>

                <?php if (empty($curriculum)): ?>
                    <div class="text-center py-12 text-gray-400">
                        <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                        Nội dung đang được cập nhật...
                    </div>
                <?php else: ?>
                    <?php foreach ($curriculum as $index => $chapter): ?>
                        <div class="chapter-accordion <?= $index === 0 ? 'open' : '' ?>" id="chapter-<?= $index ?>">
                            <button class="chapter-header" onclick="toggleChapter('chapter-<?= $index ?>')">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        <?= $index + 1 ?>
                                    </span>
                                    <span class="font-semibold text-gray-800"><?= htmlspecialchars($chapter['title']) ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400 font-medium hidden sm:block">
                                        <?= count($chapter['materials']) ?> bài giảng
                                    </span>
                                    <i class="fa-solid fa-chevron-down chevron"></i>
                                </div>
                            </button>

                            <div class="chapter-body">
                                <?php if (empty($chapter['materials'])): ?>
                                    <div class="px-6 py-4 text-sm text-gray-400 italic">Chưa có bài giảng.</div>
                                <?php else: ?>
                                    <?php foreach ($chapter['materials'] as $mat): ?>
                                        <div class="material-row">
                                            <span class="flex items-center gap-3 text-gray-700 text-sm">
                                                <?php if ($mat['type'] === 'video'): ?>
                                                    <i class="fa-solid fa-circle-play text-indigo-400 text-base w-5 text-center"></i>
                                                <?php elseif ($mat['type'] === 'file'): ?>
                                                    <i class="fa-solid fa-file-pdf text-red-400 text-base w-5 text-center"></i>
                                                <?php else: ?>
                                                    <i class="fa-solid fa-link text-blue-400 text-base w-5 text-center"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($mat['title']) ?>
                                            </span>
                                            <span class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0
                                                <?= $mat['type'] === 'video' ? 'bg-indigo-50 text-indigo-600' : ($mat['type'] === 'file' ? 'bg-red-50 text-red-500' : 'bg-blue-50 text-blue-600') ?>">
                                                <?= ucfirst($mat['type']) ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB: Chi tiết -->
        <div id="panel-detail" class="tab-panel">
            <div class="space-y-6">
                <!-- Requirements -->
                <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="section-heading">
                        <i class="fa-solid fa-clipboard-list text-indigo-500 mr-2"></i>Yêu cầu đầu vào
                    </h2>
                    <?php $req_html = $course['requirements'] ?? ''; ?>
                    <?php if ($req_html): ?>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <?= $req_html ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-400 italic">Không có yêu cầu đặc biệt. Ai cũng có thể tham gia.</p>
                    <?php endif; ?>
                </div>

                <!-- Who this course is for -->
                <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="section-heading">
                        <i class="fa-solid fa-user-group text-indigo-500 mr-2"></i>Khóa học này dành cho ai?
                    </h2>
                    <ul class="requirements-list">
                        <li>Người mới bắt đầu muốn học lập trình từ đầu</li>
                        <li>Người đã biết cơ bản và muốn hệ thống lại kiến thức</li>
                        <li>Sinh viên CNTT muốn có thêm dự án thực tế</li>
                        <li>Lập trình viên muốn chuyển sang công nghệ <?= htmlspecialchars(explode(' ', $course['title'])[0] ?? 'mới') ?></li>
                    </ul>
                </div>

                <!-- Instructor info -->
                <div class="bg-white border border-gray-100 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="section-heading">
                        <i class="fa-solid fa-chalkboard-user text-indigo-500 mr-2"></i>Thông tin giảng viên
                    </h2>
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
                            <?= mb_strtoupper(mb_substr($instructor, 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($instructor) ?></h3>
                            <p class="text-indigo-600 text-sm mb-2">Giảng viên khóa học</p>
                            <p class="text-gray-500 text-sm leading-relaxed">
                                Giảng viên có nhiều năm kinh nghiệm trong ngành lập trình. 
                                Tận tâm với học viên, giảng dạy rõ ràng và thực tiễn.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT: STICKY SIDEBAR CARD (desktop only) ===== -->
    <div class="hidden lg:block lg:w-1/3 flex-shrink-0 relative">
        <div class="sticky-card">
            <?php include __DIR__ . '/../../views/courses/_detail_card.php'; ?>
        </div>
    </div>

</div>

<script>
// Tab switching
function switchTab(name, btn) {
    // Hide all panels
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    // Show target
    document.getElementById('panel-' + name).classList.add('active');
    btn.classList.add('active');
    // Smooth scroll to content
    const panel = document.getElementById('panel-' + name);
    const offset = panel.getBoundingClientRect().top + window.scrollY - 80;
    window.scrollTo({ top: offset, behavior: 'smooth' });
}

// Chapter accordion
function toggleChapter(id) {
    const el = document.getElementById(id);
    el.classList.toggle('open');
}
</script>