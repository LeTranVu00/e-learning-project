<!-- HERO SECTION -->
<section class="flex flex-col-reverse lg:flex-row items-center justify-between py-12 lg:py-20 gap-10">
    <!-- Cột Text -->
    <div class="w-full lg:w-1/2 space-y-6 text-center lg:text-left">
        <div class="inline-block bg-yellow-100 text-primary font-semibold px-4 py-1 rounded-full text-sm mb-2">
            🚀 Nền tảng học lập trình số 1
        </div>
        <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
            Nâng tầm kỹ năng <br> 
            <span class="text-primary">Định hình tương lai</span>
        </h1>
        <p class="text-gray-500 text-lg">
            Học lập trình không khó khi bạn có một lộ trình chuẩn. Khám phá hàng trăm khóa học từ cơ bản đến chuyên sâu, thực chiến với các dự án thực tế ngay hôm nay.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
            <a href="#" class="bg-primary hover:bg-yellow-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                Khám phá khóa học
            </a>
            <a href="#" class="bg-white border-2 border-gray-200 hover:border-primary text-gray-700 hover:text-primary font-semibold py-3 px-8 rounded-lg transition">
                Tìm hiểu thêm
            </a>
        </div>
    </div>
    
    <!-- Cột Ảnh Banner -->
    <div class="w-full lg:w-1/2 flex justify-center">
        <!-- Dùng ảnh placeholder tạm, sau này bro thay bằng ảnh thật -->
        <img src="https://placehold.co/600x400/f59e0b/white?text=E-Learning+Hero+Banner" alt="Hero Banner" class="rounded-2xl shadow-2xl object-cover w-full max-w-md lg:max-w-full">
    </div>
</section>

<!-- STATS SECTION -->
<section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="p-4">
            <i class="fa-solid fa-users text-4xl text-primary mb-3"></i>
            <h3 class="text-3xl font-bold text-gray-900">10,000+</h3>
            <p class="text-gray-500 font-medium mt-1">Học viên tin tưởng</p>
        </div>
        <div class="p-4">
            <i class="fa-solid fa-book-open text-4xl text-primary mb-3"></i>
            <h3 class="text-3xl font-bold text-gray-900">500+</h3>
            <p class="text-gray-500 font-medium mt-1">Khóa học chất lượng</p>
        </div>
        <div class="p-4">
            <i class="fa-solid fa-star text-4xl text-primary mb-3"></i>
            <h3 class="text-3xl font-bold text-gray-900">4.8/5</h3>
            <p class="text-gray-500 font-medium mt-1">Đánh giá trung bình</p>
        </div>
    </div>
</section>

<!-- FEATURED COURSES SECTION -->
<section class="mb-20">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Khóa học nổi bật</h2>
            <p class="text-gray-500">Những khóa học được đăng ký nhiều nhất tháng qua.</p>
        </div>
        <a href="#" class="hidden md:inline-block text-primary font-semibold hover:underline">Xem tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a>
    </div>

    <!-- Course Grid (Sau này dùng PHP foreach để in ra chỗ này) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <?php foreach($courses as $course): ?>
        <div class="bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition duration-300 flex flex-col group">
            <div class="relative overflow-hidden">
                <?php
                    // Sử dụng ảnh mặc định nếu khóa học chưa có thumbnail
                    $thumbnail = !empty($course['thumbnail'])
                        ? htmlspecialchars($course['thumbnail'])
                        : 'https://placehold.co/600x400/f59e0b/white?text=E-Learning';
                ?>
                <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
            </div>
            <div class="p-5 flex-grow flex flex-col justify-between">
                <div>
                    <div class="text-xs text-primary font-bold tracking-wider uppercase mb-2">Khóa học</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2"><?= htmlspecialchars($course['title']) ?></h3>
                    <div class="text-sm text-gray-500 mb-4 line-clamp-2">
                        <?= htmlspecialchars(strip_tags($course['description'])) ?>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-2">
                    <span class="text-xl font-bold <?= (isset($course['price']) && $course['price'] > 0) ? 'text-primary' : 'text-green-500' ?>">
                        <?= (isset($course['price']) && $course['price'] > 0) ? number_format($course['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                    </span>
                    <a href="?action=detail&id=<?= $course['id'] ?>" class="text-primary hover:text-white border border-primary hover:bg-primary font-medium py-1.5 px-4 rounded transition">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
require_once __DIR__ . '/../app/models/Forum.php'; 
        
        // 2. Khởi tạo Model và lấy Top 3 bài viết
        $forumModel = new Forum($db);
        $topPosts = $forumModel->getTopPosts(3);
?>
<section class="py-16 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Thảo luận nổi bật</h2>
                <p class="text-gray-600">Những chủ đề đang được quan tâm và bình luận nhiều nhất.</p>
            </div>
            <a href="?action=forum" class="text-primary hover:text-yellow-600 font-bold hidden sm:flex items-center gap-2 transition">
                Xem diễn đàn <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(!empty($topPosts)): ?>
                <?php foreach($topPosts as $post): ?>
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 p-6 transition duration-300 flex flex-col h-full group">
                        
                        <div class="flex items-center gap-3 mb-4">
                            <?php 
                                // Logic Avatar thông minh giống hệt trang Diễn đàn
                                $postAvatar = !empty($post['author_avatar']) 
                                    ? $post['author_avatar'] 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random';
                            ?>
                            <img src="<?= htmlspecialchars($postAvatar) ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0">
                            <div>
                                <h4 class="font-bold text-sm text-gray-900"><?= htmlspecialchars($post['author_name']) ?></h4>
                                <p class="text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?></p>
                            </div>
                        </div>
                        
                        <div class="flex-grow mb-6">
                            <a href="?action=forum_detail&id=<?= $post['id'] ?>" class="font-bold text-xl text-gray-900 group-hover:text-primary transition line-clamp-2 mb-3">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                            <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed">
                                <?= strip_tags($post['content']) ?>
                            </p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-100 flex justify-between items-center mt-auto">
                            <span class="text-sm font-bold bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg flex items-center gap-2">
                                <i class="fa-regular fa-comment-dots"></i> <?= $post['comment_count'] ?>
                            </span>
                            <a href="?action=forum_detail&id=<?= $post['id'] ?>" class="text-sm font-bold text-gray-400 hover:text-primary transition">Đọc chi tiết &rarr;</a>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-100 border-dashed">
                    <p class="text-gray-500 italic">Chưa có bài thảo luận nào sôi nổi.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="mt-8 text-center sm:hidden">
            <a href="?action=forum" class="inline-flex justify-center items-center gap-2 bg-gray-900 text-white font-bold py-3 px-6 rounded-xl hover:bg-gray-800 transition w-full">
                Vào Diễn đàn <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>
</section>
<!-- CALL TO ACTION (BOTTOM) -->
<section class="bg-gray-900 rounded-2xl p-10 text-center relative overflow-hidden mb-10">
    <div class="relative z-10 max-w-2xl mx-auto">
        <h2 class="text-3xl font-bold text-white mb-4">Sẵn sàng bắt đầu hành trình của bạn?</h2>
        <p class="text-gray-400 mb-8">Tham gia cùng hàng ngàn học viên khác và nâng cấp kỹ năng của bạn ngay hôm nay. Đăng ký tài khoản miễn phí chỉ trong 1 phút.</p>
        <a href="#" class="bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-10 rounded-lg shadow-lg transition text-lg">Đăng ký tài khoản ngay</a>
    </div>
    <!-- Decorate circles -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-primary rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-64 h-64 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-x-1/2 translate-y-1/2"></div>
</section>