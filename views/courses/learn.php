<?php
    $all_materials = [];
    foreach ($curriculum as $chapter) {
        foreach ($chapter['materials'] as $mat) {
            $all_materials[] = $mat;
        }
    }
    $total_materials = count($all_materials);
    $completed_count = count(array_intersect(array_column($all_materials, 'id'), $completed_materials));
    $progress_percent = $total_materials > 0 ? round(($completed_count / $total_materials) * 100) : 0;
    
    function getMaterialUrl($content, $type) {
        if ($type === 'file') return htmlspecialchars($content);
        if (!preg_match("~^(?:f|ht)tps?://~i", $content)) {
            return htmlspecialchars("https://" . ltrim($content, '/'));
        }
        return htmlspecialchars($content);
    }
?>
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="learningPage(<?= $total_materials ?? 0 ?>, <?= $completed_count ?? 0 ?>)">
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4" data-aos="fade-up">
        <div>
            <a href="?action=my_courses" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition mb-2 inline-flex items-center gap-1.5 group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i> Quay lại khóa học của tôi
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Nội dung khóa học</h1>
        </div>
        
        <!-- Progress Card -->
        <div class="bg-white dark:bg-gray-800 px-5 py-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col gap-2 min-w-[220px]" 
             data-aos="fade-left">
            <div class="flex justify-between items-center text-sm font-bold text-gray-700 dark:text-gray-300">
                <span>Tiến độ học tập</span>
                <span class="text-primary" x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                <div class="h-2.5 rounded-full bg-gradient-to-r from-primary to-yellow-500 transition-all duration-700 ease-out" 
                     :class="progress == 100 ? '!bg-gradient-to-r !from-green-500 !to-emerald-500' : ''"
                     :style="'width: ' + progress + '%'"></div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 text-right">
                <span x-text="completed"></span>/<span x-text="totalMaterials"></span> bài giảng
            </p>
        </div>
    </div>

    <!-- Mini Progress Map + Info -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4" data-aos="fade-up" data-aos-delay="100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Dots Map -->
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 shrink-0">Lộ trình:</span>
                <div class="flex gap-1 flex-wrap">
                    <?php foreach ($all_materials as $i => $mat): ?>
                        <div class="w-2.5 h-2.5 rounded-full transition-all duration-300 hover:scale-150 cursor-pointer
                                    <?= in_array($mat['id'], $completed_materials) ? 'bg-green-500 shadow-sm shadow-green-300 dark:shadow-green-900' : 'bg-gray-300 dark:bg-gray-600' ?>"
                             title="<?= htmlspecialchars($mat['title']) ?>"
                             @click="scrollToMaterial(<?= $i ?>)"></div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <?php 
                    $remaining = $total_materials - $completed_count;
                    $avg_minutes = 15;
                    $remaining_hours = round(($remaining * $avg_minutes) / 60, 1);
                ?>
                <span class="flex items-center gap-1">
                    <i class="fa-regular fa-clock"></i> Còn ~<span x-text="Math.round(((totalMaterials - completed) * 15) / 60 * 10) / 10"></span> giờ
                </span>
                <span class="flex items-center gap-1">
                    <i class="fa-solid fa-check-circle text-green-500"></i> <span x-text="completed"></span> đã xong
                </span>
                <span class="flex items-center gap-1">
                    <i class="fa-solid fa-list"></i> <span x-text="totalMaterials - completed"></span> còn lại
                </span>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <?php if (empty($curriculum)): ?>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-700 dark:text-yellow-400 px-6 py-12 rounded-2xl text-center" data-aos="fade-up">
            <i class="fa-solid fa-person-digging text-5xl mb-4 opacity-50"></i>
            <p class="font-medium text-lg">Giảng viên đang cập nhật nội dung cho khóa học này.</p>
            <p class="text-sm opacity-75 mt-1">Vui lòng quay lại sau!</p>
        </div>
    <?php else: ?>

        <!-- Curriculum List -->
        <div class="space-y-4">
            <?php 
            $globalIndex = 0;
            foreach ($curriculum as $index => $chapter): 
            ?>
                
                <?php 
                    $chapterCompleted = count(array_intersect(array_column($chapter['materials'], 'id'), $completed_materials));
                ?>
                <div x-data="{ open: <?= $index == 0 ? 'true' : 'false' ?>, chapterCompleted: <?= $chapterCompleted ?>, chapterTotal: <?= count($chapter['materials']) ?> }" 
                     @material-completed.window="if ($event.detail.chapterIndex === <?= $index ?>) chapterCompleted += $event.detail.increment"
                     @open-parent-chapter="open = true"
                     class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md"
                     data-aos="fade-up" 
                     data-aos-delay="<?= $index * 50 ?>">
                    
                    <!-- Chapter Header -->
                    <button @click="open = !open" 
                            class="w-full flex justify-between items-center bg-gray-50/80 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-700/50 px-6 py-4 transition-colors">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center text-left gap-3">
                            <i class="fa-solid fa-chevron-down text-sm text-gray-400 dark:text-gray-500 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            <span class="w-8 h-8 rounded-lg bg-primary/10 dark:bg-primary/20 text-primary flex items-center justify-center font-bold text-sm shrink-0"><?= $index + 1 ?></span>
                            <?= htmlspecialchars($chapter['title']) ?>
                        </h2>
                        <span class="text-xs text-gray-400 dark:text-gray-500 font-medium hidden sm:flex items-center gap-2">
                            <span><span x-text="chapterCompleted"></span>/<?= count($chapter['materials']) ?></span>
                            <span class="w-16 h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                <span class="block h-full bg-green-500 rounded-full transition-all" :style="`width: ${chapterTotal > 0 ? Math.round((chapterCompleted / chapterTotal) * 100) : 0}%`"></span>
                            </span>
                        </span>
                    </button>
                    
                    <!-- Chapter Body -->
                    <div x-show="open" 
                         x-collapse
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="px-6 pb-4 border-t border-gray-100 dark:border-gray-700 pt-3">
                            <?php if (!empty($chapter['description'])): ?>
                                <div class="prose prose-sm dark:prose-invert max-w-none mb-4 text-gray-600 dark:text-gray-300">
                                    <?= $chapter['description'] ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (empty($chapter['materials'])): ?>
                                <p class="text-gray-400 dark:text-gray-500 text-sm py-3 italic">Chưa có bài giảng.</p>
                            <?php else: ?>
                                <ul class="space-y-2 mt-2">
                                    <?php foreach ($chapter['materials'] as $matIndex => $material): ?>
                                        <?php $currentGlobalIndex = $globalIndex++; ?>
                                        <li x-data="{ 
                                                done: <?= in_array($material['id'], $completed_materials) ? 'true' : 'false' ?>, 
                                                loading: false,
                                                materialIndex: <?= $currentGlobalIndex ?>,
                                                markDone(e, showFireworks = true) {
                                                    this.loading = true;
                                                    let formData = new FormData();
                                                    formData.append('material_id', <?= (int)$material['id'] ?>);
                                                    
                                                    fetch('?action=mark_done', { method: 'POST', body: formData })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success) { 
                                                            if (data.action === 'added') {
                                                                this.done = true;
                                                                if (showFireworks && e && e.target) {
                                                                    const btn = e.target.closest('button') || e.target.closest('a');
                                                                    if (btn) {
                                                                        const rect = btn.getBoundingClientRect();
                                                                        const x = (rect.left + rect.width / 2) / window.innerWidth;
                                                                        const y = (rect.top + rect.height / 2) / window.innerHeight;
                                                                        confetti({ particleCount: 40, spread: 60, origin: { x: x, y: y }, zIndex: 1050 });
                                                                    }
                                                                }
                                                                window.dispatchEvent(new CustomEvent('material-completed', { detail: { increment: 1, index: this.materialIndex, chapterIndex: <?= $index ?> } }));
                                                            } else if (data.action === 'removed') {
                                                                this.done = false;
                                                                window.dispatchEvent(new CustomEvent('material-completed', { detail: { increment: -1, index: this.materialIndex, chapterIndex: <?= $index ?> } }));
                                                            }
                                                        } else {
                                                            alert('Có lỗi xảy ra: ' + (data.message || 'Unknown error'));
                                                        }
                                                        this.loading = false;
                                                    })
                                                    .catch(() => { this.loading = false; });
                                                }
                                            }" 
                                            id="material-<?= $currentGlobalIndex ?>"
                                            class="flex flex-col border border-gray-100 dark:border-gray-700 p-3 rounded-xl hover:border-primary/30 dark:hover:border-primary/30 hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-200 bg-white dark:bg-gray-800 group">
                                            
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full gap-3">
                                                <!-- Material Link -->
                                                <a href="<?= getMaterialUrl($material['content'], $material['type']) ?>" 
                                                   target="_blank" 
                                                   @click="if(!done) markDone($event, false)"
                                                   class="flex items-center gap-3 flex-1 min-w-0 group/link">
                                                    <div :class="done ? 'bg-green-100 dark:bg-green-900/30 text-green-500 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 group-hover/link:bg-primary/10 group-hover/link:text-primary'" 
                                                         class="w-10 h-10 rounded-lg flex items-center justify-center transition shrink-0">
                                                        <?php if($material['type'] == 'video'): ?>
                                                            <i class="fa-solid fa-play text-sm"></i>
                                                        <?php elseif($material['type'] == 'file'): ?>
                                                            <i class="fa-solid fa-file-pdf text-sm"></i>
                                                        <?php else: ?>
                                                            <i class="fa-solid fa-link text-sm"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <span class="text-gray-700 dark:text-gray-300 font-semibold group-hover/link:text-primary dark:group-hover/link:text-primary transition line-clamp-1 text-sm block">
                                                            <?= htmlspecialchars($material['title']) ?>
                                                        </span>
                                                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full inline-block mt-0.5
                                                            <?= $material['type'] === 'video' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 
                                                               ($material['type'] === 'file' ? 'bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400' : 
                                                               'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400') ?>">
                                                            <?= ucfirst($material['type']) ?>
                                                        </span>
                                                    </div>
                                                </a>
                                                
                                                <!-- Actions -->
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <!-- Mark Done Button -->
                                                    <button @click="markDone($event, true)" :disabled="loading"
                                                        :class="done 
                                                            ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 cursor-pointer' 
                                                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer hover:border-primary dark:hover:border-primary hover:text-primary dark:hover:text-primary'"
                                                        class="text-xs font-medium border px-3 py-1.5 rounded-lg transition-all duration-200 whitespace-nowrap flex items-center justify-center min-w-[110px] shadow-sm group/btn">
                                                        
                                                        <svg x-show="loading" class="animate-spin h-3.5 w-3.5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                        </svg>

                                                        <span x-show="!loading && !done">
                                                            <i class="fa-regular fa-circle-check mr-1"></i> Xong
                                                        </span>
                                                        
                                                        <span x-show="!loading && done" x-cloak>
                                                            <span class="group-hover/btn:hidden flex items-center gap-1">
                                                                <i class="fa-solid fa-check"></i> Đã xong
                                                            </span>
                                                            <span class="hidden group-hover/btn:inline-flex items-center gap-1">
                                                                <i class="fa-solid fa-rotate-left"></i> Bỏ
                                                            </span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <?php if (!empty($material['description'])): ?>
                                            <!-- Material Description -->
                                            <div class="w-full mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-300">
                                                <?= $material['description'] ?>
                                            </div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

        <!-- Navigation Prev/Next -->
        <?php if ($total_materials > 1): ?>
        <div class="mt-8 flex justify-between items-center" data-aos="fade-up">
            <button @click="navigateMaterial(-1)" 
                    :disabled="currentMaterialIndex <= 0"
                    :class="currentMaterialIndex <= 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-medium transition bg-white dark:bg-gray-800 text-sm">
                <i class="fa-solid fa-arrow-left"></i> Bài trước
            </button>
            
            <span class="text-sm text-gray-400 dark:text-gray-500 font-medium">
                Bài <span x-text="currentMaterialIndex + 1"></span>/<span x-text="totalMaterials"></span>
            </span>
            
            <button @click="navigateMaterial(1)" 
                    :disabled="currentMaterialIndex >= totalMaterials - 1"
                    :class="currentMaterialIndex >= totalMaterials - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-primary hover:text-white'"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-primary text-primary font-medium transition bg-white dark:bg-gray-800 text-sm">
                Bài tiếp theo <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- Completion Message -->
        <div x-show="completed >= total && total > 0" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-cloak
             class="mt-8 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-8 text-center" 
             data-aos="zoom-in">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg animate-bounce-slow">
                <i class="fa-solid fa-trophy text-4xl text-white"></i>
            </div>
            <h3 class="text-2xl font-bold text-green-800 dark:text-green-400 mb-2">🎉 Chúc mừng! Bạn đã hoàn thành khóa học!</h3>
            <p class="text-green-600 dark:text-green-500 mb-6">Hãy tiếp tục khám phá thêm nhiều khóa học khác để nâng cao kỹ năng.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="?action=courses" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-compass"></i> Khám phá thêm
                </a>
                <a href="?action=my_courses" class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 font-semibold py-3 px-6 rounded-xl transition-all">
                    <i class="fa-solid fa-list-check"></i> Khóa học của tôi
                </a>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Confetti Library -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>

<script>
function triggerConfetti() {
    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
    setTimeout(() => confetti({ particleCount: 50, angle: 60, spread: 55, origin: { x: 0 } }), 300);
    setTimeout(() => confetti({ particleCount: 50, angle: 120, spread: 55, origin: { x: 1 } }), 600);
    setTimeout(() => confetti({ particleCount: 80, spread: 100, origin: { y: 0.5 } }), 900);
}

document.addEventListener('alpine:init', () => {
    Alpine.data('learningPage', (total, completed) => ({
        totalMaterials: total,
        completed: completed,
        progress: total > 0 ? Math.round((completed / total) * 100) : 0,
        currentMaterialIndex: 0,
        
        init() {
            window.addEventListener('material-completed', (e) => {
                this.completed += e.detail.increment;
                if (e.detail.index !== undefined) {
                    this.currentMaterialIndex = e.detail.index;
                }
                this.progress = this.totalMaterials > 0 ? Math.round((this.completed / this.totalMaterials) * 100) : 0;
                window.dispatchEvent(new CustomEvent('progress-updated', { detail: { progress: this.progress } }));
                
                if (this.completed >= this.totalMaterials && e.detail.increment > 0) {
                    triggerConfetti();
                }
            });
        },
        
        scrollToMaterial(index) {
            this.currentMaterialIndex = index;
            const el = document.getElementById('material-' + index);
            if (el) {
                el.dispatchEvent(new CustomEvent('open-parent-chapter', { bubbles: true }));
                setTimeout(() => {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('ring-2', 'ring-primary', 'ring-offset-2');
                    setTimeout(() => el.classList.remove('ring-2', 'ring-primary', 'ring-offset-2'), 2000);
                }, 150);
            }
        },
        
        navigateMaterial(direction) {
            const newIndex = this.currentMaterialIndex + direction;
            if (newIndex >= 0 && newIndex < this.totalMaterials) {
                this.scrollToMaterial(newIndex);
            }
        }
    }));
});
</script>