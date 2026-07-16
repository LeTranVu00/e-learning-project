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
$start_date     = $course['start_date']     ?? null;
$schedule       = $course['schedule']       ?? null;
$study_time     = $course['study_time']     ?? null;
$contact_phone  = $course['contact_phone']  ?? null;
?>

<style>
.course-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #1e3a5f 100%);
    position: relative;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    margin-top: -2rem;
}
.dark .course-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0f172a 100%);
}
.course-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 70% 50%, rgba(139, 92, 246, 0.25) 0%, transparent 65%),
                radial-gradient(ellipse at 20% 80%, rgba(59, 130, 246, 0.2) 0%, transparent 50%);
    pointer-events: none;
}

.course-detail-shell {
    width: min(100%, 80rem);
    margin-inline: auto;
    position: relative;
    z-index: 20;
    padding-inline: 1rem;
}
.course-main-column,
.course-sidebar-column { min-width: 0; }
.course-sidebar-column { position: relative; align-self: stretch; }

@media (min-width: 640px) { .course-detail-shell { padding-inline: 1.5rem; } }
@media (min-width: 1024px) {
    .course-detail-shell {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(320px, 420px);
        gap: 2.5rem;
        align-items: stretch;
        margin-top: 2.25rem;
    }
}
@media (min-width: 1280px) { .course-detail-shell { padding-inline: 0; } }

.detail-tabs-holder { margin-bottom: 2.5rem; }
.detail-tabs {
    position: sticky;
    top: 5.5rem;
    z-index: 40;
    background: rgba(255, 255, 255, 0.94);
    border-radius: 22px;
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    padding: 8px;
    backdrop-filter: blur(10px);
}
.dark .detail-tabs {
    background: rgba(31, 41, 55, 0.94);
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.3);
}

.tab-btn {
    flex: 1 0 180px;
    justify-content: center;
    min-height: 52px;
    padding: 14px 24px;
    font-weight: 600;
    font-size: 1rem;
    color: #111827;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.2s;
    background: #f3f4f6;
    border: none;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.dark .tab-btn { color: #e5e7eb; background: #374151; }
.tab-btn:hover { color: #4f46e5; background: #eef2ff; }
.dark .tab-btn:hover { color: #818cf8; background: #1e293b; }
.tab-btn.active {
    color: #fff;
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #0ea5e9 100%);
    box-shadow: 0 8px 22px rgba(37, 99, 235, 0.24);
}
.tab-btn.active span { background: rgba(255, 255, 255, 0.18); color: #fff; }
.tab-panel { display: block; scroll-margin-top: 6rem; }
.tab-panel + .tab-panel { margin-top: 2rem; }

.sticky-card {
    position: sticky;
    z-index: 50;
    height: fit-content;
    top: 5.5rem;
}

.chapter-accordion {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 10px;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s;
}
.dark .chapter-accordion { background: #1f2937; border-color: #374151; }
.chapter-accordion:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.dark .chapter-accordion:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
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
.dark .chapter-header { background: #111827; }
.chapter-header:hover { background: #f3f4f6; }
.dark .chapter-header:hover { background: #1f2937; }
.chapter-header .chevron { transition: transform 0.3s; color: #9ca3af; font-size: 0.8rem; }
.chapter-accordion.open .chapter-header .chevron { transform: rotate(180deg); }
.chapter-body { display: none; border-top: 1px solid #f3f4f6; }
.dark .chapter-body { border-color: #374151; }
.chapter-accordion.open .chapter-body { display: block; }
.material-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    border-bottom: 1px solid #f9fafb;
    transition: background 0.15s;
}
.dark .material-row { border-color: #1f2937; }
.material-row:last-child { border-bottom: none; }
.material-row:hover { background: #fafafa; }
.dark .material-row:hover { background: #1f2937; }

.section-heading {
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
}
.dark .section-heading { color: #f9fafb; border-color: #374151; }

.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- ==================== HERO SECTION ==================== -->
<div class="course-hero px-4 sm:px-6 lg:px-8 py-10 lg:pt-14 lg:pb-28">
    <div class="max-w-7xl mx-auto relative z-10">
        <nav class="flex items-center gap-2 text-sm text-indigo-300 mb-6" data-aos="fade-right">
            <a href="?action=home" class="hover:text-white transition">Trang chủ</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <a href="?action=courses" class="hover:text-white transition">Khóa học</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span class="text-white font-medium"><?= htmlspecialchars(mb_strimwidth($course['title'], 0, 50, '...')) ?></span>
        </nav>

        <div class="lg:flex lg:gap-12 lg:items-start">
            <div class="lg:w-2/3" data-aos="fade-up">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight mb-4">
                    <?= htmlspecialchars($course['title']) ?>
                </h1>
                <p class="text-indigo-200 text-base lg:text-lg mb-6 leading-relaxed max-w-2xl">
                    <?= mb_strimwidth(strip_tags($course['description'] ?? 'Khóa học chất lượng cao cho người muốn nâng cao kỹ năng lập trình.'), 0, 200, '...') ?>
                </p>

                <div class="flex flex-wrap items-center gap-3 mb-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                        <i class="fa-solid fa-trophy"></i> Bestseller
                    </span>
                    <div class="flex items-center gap-1 text-yellow-400">
                        <i class="fa-solid fa-star text-sm"></i><i class="fa-solid fa-star text-sm"></i><i class="fa-solid fa-star text-sm"></i><i class="fa-solid fa-star text-sm"></i><i class="fa-solid fa-star-half-stroke text-sm"></i>
                        <span class="text-white font-semibold ml-1">4.8</span>
                        <span class="text-indigo-300 text-sm">(<?= number_format($total_materials_count * 47 + 258) ?> đánh giá)</span>
                    </div>
                    <span class="text-indigo-300 text-sm">
                        <i class="fa-solid fa-users mr-1"></i><?= number_format($total_materials_count * 31 + 1234) ?> học viên
                    </span>
                </div>

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
        </div>
    </div>
</div>

<!-- ==================== MAIN CONTENT + SIDEBAR ==================== -->
<div class="course-detail-shell mb-20">
    <div class="course-main-column">
        <!-- Mobile Sidebar -->
        <div class="lg:hidden mb-8" data-aos="fade-up">
            <?php include __DIR__ . '/../../views/courses/_detail_card.php'; ?>
        </div>

        <!-- Tabs -->
        <div class="detail-tabs" data-aos="fade-up" data-aos-delay="100" style="margin-bottom: 2.5rem;">
            <div class="flex gap-2 overflow-x-auto hide-scrollbar">
                <button type="button" class="tab-btn active" onclick="switchTab('overview')" id="tab-overview">
                    Tổng quan
                </button>
                <button type="button" class="tab-btn" onclick="switchTab('curriculum')" id="tab-curriculum">
                    Lộ trình
                    <span class="ml-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-xs font-bold px-2 py-0.5 rounded-full"><?= $total_chapters ?></span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab('detail')" id="tab-detail">
                    Chi tiết
                </button>
            </div>
        </div>

        <!-- TAB: Overview -->
        <div id="panel-overview" class="tab-panel active">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm mb-8" data-aos="fade-up">
                <h2 class="section-heading">
                    <i class="fa-solid fa-graduation-cap text-indigo-500 mr-2"></i>Bạn sẽ học được gì?
                </h2>
                <?php $benefits_html = $course['benefits'] ?? ''; ?>
                <?php if ($benefits_html): ?>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed">
                        <?= $benefits_html ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400 dark:text-gray-500 italic">Thông tin đang được cập nhật...</p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-8" data-aos="fade-up" data-aos-delay="50">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-4 text-center shadow-sm">
                    <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-1"><?= $total_chapters ?></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Chương học</div>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-4 text-center shadow-sm">
                    <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-1"><?= $total_materials_count ?></div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Bài giảng</div>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-4 text-center shadow-sm">
                    <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-1"><?= $duration_hours > 0 ? $duration_hours : '—' ?>h</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Giờ học</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                <h2 class="section-heading">
                    <i class="fa-solid fa-file-lines text-gray-400 dark:text-gray-500 mr-2"></i>Mô tả khóa học
                </h2>
                <div class="text-gray-600 dark:text-gray-300 leading-relaxed prose dark:prose-invert max-w-none">
                    <?= $course['description'] ?? '<p class="italic text-gray-400 dark:text-gray-500">Chưa có mô tả.</p>' ?>
                </div>
            </div>
        </div>

        <!-- TAB: Curriculum -->
        <div id="panel-curriculum" class="tab-panel" data-aos="fade-up">
            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="section-heading mb-0" style="border:none; padding:0;">
                        <i class="fa-solid fa-map-pin text-indigo-500 mr-2"></i>Lộ trình khóa học
                    </h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                        <?= $total_chapters ?> chương • <?= $total_materials_count ?> bài
                    </span>
                </div>

                <?php if (empty($curriculum)): ?>
                    <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                        <i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>
                        Nội dung đang được cập nhật...
                    </div>
                <?php else: ?>
                    <?php foreach ($curriculum as $index => $chapter): ?>
                        <div class="chapter-accordion <?= $index === 0 ? 'open' : '' ?>" id="chapter-<?= $index ?>">
                            <button class="chapter-header" onclick="toggleChapter('chapter-<?= $index ?>')">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm flex-shrink-0"><?= $index + 1 ?></span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200"><?= htmlspecialchars($chapter['title']) ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-medium hidden sm:block"><?= count($chapter['materials']) ?> bài</span>
                                    <i class="fa-solid fa-chevron-down chevron"></i>
                                </div>
                            </button>
                            <div class="chapter-body">
                                <?php if (empty($chapter['materials'])): ?>
                                    <div class="px-6 py-4 text-sm text-gray-400 dark:text-gray-500 italic">Chưa có bài giảng.</div>
                                <?php else: ?>
                                    <?php foreach ($chapter['materials'] as $mat): ?>
                                        <div class="material-row">
                                            <span class="flex items-center gap-3 text-gray-700 dark:text-gray-300 text-sm">
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
                                                <?= $mat['type'] === 'video' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : ($mat['type'] === 'file' ? 'bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400') ?>">
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

        <!-- TAB: Detail -->
        <div id="panel-detail" class="tab-panel">
            <div class="space-y-6" data-aos="fade-up">
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="section-heading">
                        <i class="fa-solid fa-clipboard-list text-indigo-500 mr-2"></i>Yêu cầu đầu vào
                    </h2>
                    <?php $req_html = $course['requirements'] ?? ''; ?>
                    <?php if ($req_html): ?>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed"><?= $req_html ?></div>
                    <?php else: ?>
                        <p class="text-gray-400 dark:text-gray-500 italic">Không có yêu cầu đặc biệt.</p>
                    <?php endif; ?>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="section-heading">
                        <i class="fa-solid fa-user-group text-indigo-500 mr-2"></i>Khóa học này dành cho ai?
                    </h2>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-500 mt-2 shrink-0"></i> Người mới bắt đầu muốn học lập trình từ đầu</li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-500 mt-2 shrink-0"></i> Người đã biết cơ bản và muốn hệ thống lại kiến thức</li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-500 mt-2 shrink-0"></i> Sinh viên CNTT muốn có thêm dự án thực tế</li>
                        <li class="flex items-start gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-500 mt-2 shrink-0"></i> Lập trình viên muốn chuyển sang công nghệ mới</li>
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <h2 class="section-heading">
                        <i class="fa-solid fa-chalkboard-user text-indigo-500 mr-2"></i>Thông tin giảng viên
                    </h2>
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl flex-shrink-0">
                            <?= mb_strtoupper(mb_substr($instructor, 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-lg"><?= htmlspecialchars($instructor) ?></h3>
                            <p class="text-indigo-600 dark:text-indigo-400 text-sm mb-2">Giảng viên khóa học</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Giảng viên có nhiều năm kinh nghiệm trong ngành lập trình. Tận tâm với học viên, giảng dạy rõ ràng và thực tiễn.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="course-sidebar-column hidden lg:block">
        <div class="sticky-card">
            <?php include __DIR__ . '/../../views/courses/_detail_card.php'; ?>
        </div>
    </div>
</div>

<script>
function setActiveTab(name) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById('tab-' + name);
    if (activeBtn) activeBtn.classList.add('active');
}
function switchTab(name) {
    const panel = document.getElementById('panel-' + name);
    if (!panel) return;
    setActiveTab(name);
    const tabs = document.querySelector('.detail-tabs');
    const navOffset = tabs ? tabs.offsetHeight + 40 : 96;
    const offset = panel.getBoundingClientRect().top + window.scrollY - navOffset;
    window.scrollTo({ top: offset, behavior: 'smooth' });
}
function toggleChapter(id) { document.getElementById(id).classList.toggle('open'); }

document.addEventListener('DOMContentLoaded', function() {
    // The native CSS position: sticky handles the fixed scroll behavior perfectly without needing JS calculation.

    const sections = ['overview', 'curriculum', 'detail']
        .map(n => ({ name: n, el: document.getElementById('panel-' + n) }))
        .filter(s => s.el);

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            const visible = entries.filter(e => e.isIntersecting).sort((a,b) => b.intersectionRatio - a.intersectionRatio)[0];
            if (visible) {
                const current = sections.find(s => s.el === visible.target);
                if (current) setActiveTab(current.name);
            }
        }, { rootMargin: '-100px 0px -50% 0px', threshold: [0.1, 0.3, 0.5] });
        sections.forEach(s => observer.observe(s.el));
    }
});
</script>