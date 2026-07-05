<div class="mb-8" data-aos="fade-up">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
        <i class="fa-solid fa-book-open text-primary mr-2"></i>Khóa học của tôi
    </h1>
    <p class="text-gray-600 dark:text-gray-400 mt-2">Tiếp tục hành trình học tập và hoàn thiện kỹ năng của bạn</p>
</div>

<?php if (empty($courses)): ?>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center" data-aos="fade-up">
        <div class="w-24 h-24 bg-yellow-50 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-box-open text-4xl text-primary"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Bạn chưa ghi danh khóa học nào</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Hãy khám phá các khóa học hấp dẫn của chúng tôi và bắt đầu ngay hôm nay!</p>
        <a href="?action=home" class="inline-flex items-center gap-2 bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <i class="fa-solid fa-compass"></i> Khám phá ngay
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($courses as $index => $course): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl dark:hover:shadow-gray-900/30 transition-all duration-300 flex flex-col group hover:-translate-y-2" 
                 data-aos="fade-up" 
                 data-aos-delay="<?= ($index % 6) * 50 ?>">
                
                <!-- Thumbnail -->
                <div class="relative h-48 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                    <img src="<?= htmlspecialchars($course['thumbnail'] ?? 'https://placehold.co/600x400/f59e0b/white?text=Course+Image') ?>" 
                         alt="Course Thumbnail" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <!-- Enrolled Badge -->
                    <div class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-green-600 dark:text-green-400 shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-check-circle"></i> Đã ghi danh
                    </div>
                    
                    <!-- Progress Badge -->
                    <?php if ($course['progress_percent'] == 100): ?>
                        <div class="absolute top-4 left-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                            <i class="fa-solid fa-trophy"></i> Hoàn thành
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                        <?= htmlspecialchars($course['title']) ?>
                    </h3>
                    
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-check"></i>
                        Đăng ký: <?= date('d/m/Y', strtotime($course['enrolled_at'])) ?>
                    </p>

                    <!-- Last accessed -->
                    <?php if (!empty($course['last_accessed'])): ?>
                        <p class="text-gray-400 dark:text-gray-500 text-xs mb-4 flex items-center gap-1.5">
                            <i class="fa-regular fa-clock"></i>
                            Học gần nhất: <?= date('d/m/Y H:i', strtotime($course['last_accessed'])) ?>
                        </p>
                    <?php endif; ?>

                    <div class="mt-auto">
                        <!-- Progress Bar -->
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tiến độ học tập</span>
                            <span class="text-sm font-bold <?= $course['progress_percent'] == 100 ? 'text-green-500' : 'text-primary' ?>">
                                <?= $course['progress_percent'] ?>%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-6 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-1000 ease-out
                                        <?= $course['progress_percent'] == 100 ? 'bg-gradient-to-r from-green-500 to-emerald-500' : 'bg-gradient-to-r from-primary to-yellow-500' ?>" 
                                 style="width: <?= $course['progress_percent'] ?>%">
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="?action=learn&id=<?= $course['id'] ?>" 
                           class="block w-full text-center border-2 border-primary text-primary hover:bg-primary hover:text-white dark:hover:text-white font-bold py-2.5 rounded-xl transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5 group/btn">
                            <?php if ($course['progress_percent'] == 0): ?>
                                <i class="fa-solid fa-play mr-1"></i> Bắt đầu học
                            <?php elseif ($course['progress_percent'] == 100): ?>
                                <i class="fa-solid fa-rotate-right mr-1"></i> Học lại
                            <?php else: ?>
                                <i class="fa-solid fa-arrow-right mr-1 group-hover/btn:translate-x-1 transition-transform"></i> Vào học tiếp
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Stats Summary -->
    <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6" data-aos="fade-up">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-white"><?= count($courses) ?></div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Khóa học đã đăng ký</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-trophy text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-white">
                <?= count(array_filter($courses, fn($c) => $c['progress_percent'] == 100)) ?>
            </div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Đã hoàn thành</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 text-center shadow-sm">
            <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-chart-line text-primary text-xl"></i>
            </div>
            <?php 
                $avgProgress = count($courses) > 0 
                    ? round(array_sum(array_column($courses, 'progress_percent')) / count($courses)) 
                    : 0;
            ?>
            <div class="text-2xl font-extrabold text-gray-900 dark:text-white"><?= $avgProgress ?>%</div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Tiến độ trung bình</p>
        </div>
    </div>
<?php endif; ?>