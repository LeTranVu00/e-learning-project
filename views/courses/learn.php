<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-dark">Nội dung bài giảng</h1>
        <a href="?action=my_courses" class="text-sm font-medium text-gray-500 hover:text-primary transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <?php if (empty($curriculum)): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-6 py-8 rounded-xl text-center">
            <i class="fa-solid fa-person-digging text-4xl mb-3"></i>
            <p class="font-medium">Giảng viên đang cập nhật nội dung cho khóa học này. Vui lòng quay lại sau!</p>
        </div>
    <?php else: ?>

        <div class="space-y-4">
            <?php foreach ($curriculum as $chapter): ?>
                
                <div x-data="{ open: true }" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    
                    <button @click="open = !open" class="w-full flex justify-between items-center bg-gray-50/50 hover:bg-gray-50 px-6 py-4 transition">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fa-solid fa-chevron-down text-sm mr-3 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                            <?= htmlspecialchars($chapter['title']) ?>
                        </h2>
                    </button>
                    
                    <div x-show="open" x-collapse>
                        <div class="px-6 pb-4 border-t border-gray-100 pt-2">
                            <?php if (empty($chapter['materials'])): ?>
                                <p class="text-gray-400 text-sm py-2 italic">Chưa có tài liệu.</p>
                            <?php else: ?>
                                <ul class="space-y-3 mt-3">
                                    <?php foreach ($chapter['materials'] as $material): ?>
                                        <li x-data="{ 
                                                done: <?= isset($material['is_done']) && $material['is_done'] ? 'true' : 'false' ?>, 
                                                loading: false, 
                                                markDone() {
                                                    if(this.done) return;
                                                    this.loading = true;
                                                    
                                                    let formData = new FormData();
                                                    formData.append('material_id', <?= (int)$material['id'] ?>);
                                                    
                                                    fetch('?action=mark_done', { method: 'POST', body: formData })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success) { 
                                                            this.done = true;
                                                        }
                                                        this.loading = false;
                                                    })
                                                    .catch(() => { this.loading = false; });
                                                }
                                            }" 
                                            class="flex flex-col sm:flex-row sm:items-center justify-between border border-gray-200 p-4 rounded-xl hover:border-primary/50 hover:shadow-md transition bg-white group">
                                            
                                            <div class="flex items-center gap-4 mb-3 sm:mb-0">
                                                <div :class="done ? 'bg-green-50 text-green-500' : 'bg-blue-50 text-blue-500 group-hover:bg-primary group-hover:text-white'" class="w-10 h-10 rounded-lg flex items-center justify-center transition">
                                                    <?php if($material['type'] == 'video'): ?>
                                                        <i class="fa-solid fa-play"></i>
                                                    <?php elseif($material['type'] == 'file'): ?>
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-link"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <a href="<?= htmlspecialchars($material['file_url']) ?>" target="_blank" class="text-gray-700 font-semibold hover:text-primary transition line-clamp-1">
                                                    <?= htmlspecialchars($material['title']) ?>
                                                </a>
                                            </div>
                                            
                                            <button @click="markDone()" :disabled="loading || done"
                                                :class="done ? 'bg-green-100 text-green-700 border-green-200 cursor-default' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 cursor-pointer'"
                                                class="text-sm font-medium border px-4 py-2 rounded-lg transition whitespace-nowrap flex items-center justify-center min-w-[140px]">
                                                
                                                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>

                                                <span x-show="!loading && !done">Mark as done</span>
                                                
                                                <span x-show="!loading && done" x-cloak>
                                                    <i class="fa-solid fa-check mr-1"></i> Done
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