<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-8 mb-16">
    <div class="flex flex-col md:flex-row">
        <div class="md:w-1/2">
            <img src="<?= htmlspecialchars($course['thumbnail'] ?? 'https://placehold.co/800x600?text=No+Image') ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="w-full h-full object-cover">
        </div>
        
        <div class="md:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($course['title']) ?></h1>
            
            <div class="flex items-center text-sm text-gray-500 mb-6 gap-4">
                <span><i class="fa-solid fa-users text-primary mr-1"></i> 1,234 học viên</span>
                <span><i class="fa-solid fa-clock text-primary mr-1"></i> 15 giờ học</span>
            </div>
            
            <div class="text-gray-600 text-lg mb-8 leading-relaxed prose max-w-none">
                <?= $course['description'] ?? 'Chưa có mô tả chi tiết cho khóa học này.' ?>
            </div>
            
            <div class="flex items-center gap-6 mt-auto">
                <span class="text-3xl font-extrabold text-green-500">Miễn phí</span>
                
                <a href="?action=enroll_course&id=<?= $course['id'] ?>" class="block text-center bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg w-full">
                    Đăng ký học ngay
                </a>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-10 mt-10 mb-16">
    
    <div class="lg:w-2/3 space-y-10">
        
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Bạn sẽ học được gì?</h2>
            <div class="prose max-w-none text-gray-700 prose-ul:list-disc prose-ul:ml-5 prose-li:marker:text-green-500">
                <?= $course['benefits'] ?? '<p class="italic text-gray-500">Thông tin đang được cập nhật...</p>' ?>
            </div>
        </div>

        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Nội dung khóa học</h2>
            <div class="flex justify-between items-center mb-6">
                <?php 
                    $total_chapters = count($curriculum ?? []);
                    $total_materials = 0;
                    if(!empty($curriculum)) {
                        foreach($curriculum as $c) { $total_materials += count($c['materials']); }
                    }
                ?>
                <p class="text-sm text-gray-500">Bao gồm <?= $total_chapters ?> chương • <?= $total_materials ?> bài giảng</p>
            </div>
            
            <div class="flex flex-col gap-3">
                <?php if (empty($curriculum)): ?>
                    <div class="border border-gray-200 text-gray-500 px-6 py-8 rounded-xl text-center italic bg-white shadow-sm">
                        Nội dung đang được cập nhật...
                    </div>
                <?php else: ?>
                    <?php foreach ($curriculum as $index => $chapter): ?>
                        <div x-data="{ expanded: <?= $index == 0 ? 'true' : 'false' ?> }" class="border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            
                            <button @click="expanded = !expanded" class="w-full px-5 py-4 flex justify-between items-center text-left bg-gray-50 hover:bg-gray-100 transition duration-200">
                                <span class="font-bold text-gray-800"><?= htmlspecialchars($chapter['title']) ?></span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-500 font-medium"><?= count($chapter['materials']) ?> bài</span>
                                    <i class="fa-solid fa-chevron-down text-gray-500 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></i>
                                </div>
                            </button>
                            
                            <div x-show="expanded" x-collapse x-cloak class="px-5 py-2 divide-y divide-gray-100">
                                <?php if (empty($chapter['materials'])): ?>
                                    <div class="py-3 text-sm text-gray-400 italic">Chưa có bài giảng.</div>
                                <?php else: ?>
                                    <?php foreach ($chapter['materials'] as $material): ?>
                                        <div class="py-3 flex justify-between items-center group cursor-default">
                                            <span class="text-gray-600 transition flex items-center gap-2">
                                                <?php if($material['type'] == 'video'): ?>
                                                    <i class="fa-solid fa-circle-play text-primary/70"></i>
                                                <?php elseif($material['type'] == 'file'): ?>
                                                    <i class="fa-solid fa-file-pdf text-red-400"></i>
                                                <?php else: ?>
                                                    <i class="fa-solid fa-link text-blue-400"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($material['title']) ?>
                                            </span>
                                            <span class="text-xs font-bold text-gray-400 uppercase bg-gray-100 px-2 py-1 rounded"><?= $material['type'] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="lg:w-1/3 space-y-8">
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Yêu cầu khóa học</h3>
            <div class="prose max-w-none text-gray-600 prose-ul:list-disc prose-ul:ml-5 prose-li:marker:text-gray-400">
                <?= $course['requirements'] ?? '<p class="italic text-gray-500 text-sm">Không có yêu cầu đặc biệt.</p>' ?>
            </div>
        </div>
    </div>
</div>