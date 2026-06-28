<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
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
        
        // Helper function xử lý URL an toàn
        function getMaterialUrl($content, $type) {
            if ($type === 'file') return htmlspecialchars($content);
            if (!preg_match("~^(?:f|ht)tps?://~i", $content)) {
                return htmlspecialchars("https://" . ltrim($content, '/'));
            }
            return htmlspecialchars($content);
        }
    ?>

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="?action=my_courses" class="text-sm font-medium text-gray-500 hover:text-primary transition mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-dark">Nội dung khóa học</h1>
        </div>
        
        <div class="bg-white px-5 py-3 rounded-xl shadow-sm border border-gray-100 flex flex-col gap-2 min-w-[200px]" x-data="{ progress: <?= $progress_percent ?> }" @progress-updated.window="progress = $event.detail.progress">
            <div class="flex justify-between items-center text-sm font-bold text-gray-700">
                <span>Tiến độ học tập</span>
                <span class="text-primary" x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-500" :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    </div>

    <?php if (empty($curriculum)): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-6 py-8 rounded-xl text-center">
            <i class="fa-solid fa-person-digging text-4xl mb-3"></i>
            <p class="font-medium">Giảng viên đang cập nhật nội dung cho khóa học này. Vui lòng quay lại sau!</p>
        </div>
    <?php else: ?>

        <div class="space-y-4" x-data="progressTracker(<?= $total_materials ?>, <?= $completed_count ?>)">
            <?php foreach ($curriculum as $index => $chapter): ?>
                
                <div x-data="{ open: <?= $index == 0 ? 'true' : 'false' ?> }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    
                    <button @click="open = !open" class="w-full flex justify-between items-center bg-gray-50/80 hover:bg-gray-100 px-6 py-4 transition">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center text-left">
                            <i class="fa-solid fa-chevron-down text-sm mr-3 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            <?= htmlspecialchars($chapter['title']) ?>
                        </h2>
                    </button>
                    
                    <div x-show="open" x-collapse>
                        <div class="px-6 pb-4 border-t border-gray-100 pt-2">
                            <?php if (empty($chapter['materials'])): ?>
                                <p class="text-gray-400 text-sm py-2 italic">Chưa có bài giảng.</p>
                            <?php else: ?>
                                <ul class="space-y-3 mt-3">
                                    <?php foreach ($chapter['materials'] as $material): ?>
                                        <li x-data="{ 
                                                done: <?= in_array($material['id'], $completed_materials) ? 'true' : 'false' ?>, 
                                                loading: false, 
                                                markDone() {
                                                    this.loading = true;
                                                    
                                                    let formData = new FormData();
                                                    formData.append('material_id', <?= (int)$material['id'] ?>);
                                                    
                                                    fetch('?action=mark_done', { method: 'POST', body: formData })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success) { 
                                                            if (data.action === 'added') {
                                                                this.done = true;
                                                                $dispatch('material-completed', { increment: 1 });
                                                            } else if (data.action === 'removed') {
                                                                this.done = false;
                                                                $dispatch('material-completed', { increment: -1 });
                                                            }
                                                        }
                                                        this.loading = false;
                                                    })
                                                    .catch(() => { this.loading = false; });
                                                }
                                            }" 
                                            class="flex flex-col sm:flex-row sm:items-center justify-between border border-gray-100 p-3 rounded-xl hover:border-primary/40 hover:shadow-md hover:bg-gray-50 transition bg-white group">
                                            
                                            <a href="<?= getMaterialUrl($material['content'], $material['type']) ?>" target="_blank" class="flex items-center gap-4 mb-3 sm:mb-0 group-hover:bg-transparent flex-1 group/link">
                                                <div :class="done ? 'bg-green-50 text-green-500' : 'bg-gray-100 text-gray-400 group-hover/link:bg-primary group-hover/link:text-white'" class="w-10 h-10 rounded-lg flex items-center justify-center transition shrink-0">
                                                    <?php if($material['type'] == 'video'): ?>
                                                        <i class="fa-solid fa-play"></i>
                                                    <?php elseif($material['type'] == 'file'): ?>
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-link"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-gray-700 font-semibold group-hover/link:text-primary transition line-clamp-1">
                                                    <?= htmlspecialchars($material['title']) ?>
                                                </span>
                                            </a>
                                            
                                            <button @click="markDone()" :disabled="loading"
                                                :class="done ? 'bg-green-100 text-green-700 border-green-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 cursor-pointer' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 cursor-pointer hover:border-primary hover:text-primary'"
                                                class="text-sm font-medium border px-4 py-2 rounded-lg transition whitespace-nowrap flex items-center justify-center min-w-[140px] shadow-sm group/btn">
                                                
                                                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>

                                                <span x-show="!loading && !done">Đánh dấu hoàn thành</span>
                                                
                                                <span x-show="!loading && done" x-cloak>
                                                    <span class="group-hover/btn:hidden"><i class="fa-solid fa-check mr-1"></i> Đã hoàn thành</span>
                                                    <span class="hidden group-hover/btn:inline"><i class="fa-solid fa-xmark mr-1"></i> Bỏ đánh dấu</span>
                                                </span>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('progressTracker', (total, completed) => ({
        total: total,
        completed: completed,
        
        init() {
            window.addEventListener('material-completed', (e) => {
                this.completed += e.detail.increment;
                let newProgress = Math.round((this.completed / this.total) * 100);
                window.dispatchEvent(new CustomEvent('progress-updated', { detail: { progress: newProgress } }));
            });
        }
    }));
});
</script>