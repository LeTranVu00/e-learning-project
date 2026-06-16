<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Khóa học của tôi</h1>
    <p class="text-gray-600 mt-2">Tiếp tục hành trình học tập và hoàn thiện kỹ năng của bạn</p>
</div>

<?php if (empty($courses)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-24 h-24 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-box-open text-4xl text-primary"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Bạn chưa ghi danh khóa học nào</h3>
        <p class="text-gray-500 mb-6">Hãy khám phá các khóa học hấp dẫn của chúng tôi và bắt đầu ngay hôm nay!</p>
        <a href="?action=home" class="inline-block bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg">
            Khám phá ngay
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($courses as $course): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 flex flex-col">
                <div class="relative h-48 bg-gray-200">
                    <img src="<?= htmlspecialchars($course['thumbnail'] ?? 'https://placehold.co/600x400?text=Course+Image') ?>" alt="Course Thumbnail" class="w-full h-full object-cover">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-green-600 shadow-sm">
                        <i class="fa-solid fa-check-circle mr-1"></i> Đã ghi danh
                    </div>
                </div>

                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                        <?= htmlspecialchars($course['title']) ?>
                    </h3>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                        Ngày đăng ký: <?= date('d/m/Y', strtotime($course['enrolled_at'])) ?>
                    </p>

                    <div class="mt-auto">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Tiến độ học tập</span>
                            <span class="text-sm font-bold text-primary"><?= $course['progress_percent'] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                            <div class="bg-primary h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                 style="width: <?= $course['progress_percent'] ?>%">
                            </div>
                        </div>

                        <a href="?action=learn&id=<?= $course['id'] ?>" class="block w-full text-center border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-2.5 rounded-xl transition">
                            Vào học tiếp <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>