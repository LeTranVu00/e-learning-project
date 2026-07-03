<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-10 text-center">
        <?php if (!empty($category_info)): ?>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Khóa học: <?= htmlspecialchars($category_info['name']) ?></h1>
        <?php else: ?>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Khám phá Khóa học</h1>
        <?php endif; ?>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">Tìm kiếm và lựa chọn lộ trình học tập phù hợp nhất với mục tiêu của bạn. Hàng trăm khóa học chất lượng đang chờ đón.</p>
    </div>

    <!-- Bộ lọc & Tìm kiếm -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-10">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="action" value="courses">
            
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass text-gray-400 absolute left-4 top-3.5"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tìm kiếm tên khóa học, giảng viên..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-primary transition text-sm">
            </div>
            
            <div class="w-full md:w-64">
                <select name="sort" onchange="this.form.submit()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-primary transition text-sm font-medium text-gray-700 cursor-pointer">
                    <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                    <option value="price_low" <?= ($_GET['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Giá từ thấp đến cao</option>
                    <option value="price_high" <?= ($_GET['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Giá từ cao đến thấp</option>
                </select>
            </div>
            
            <button type="submit" class="bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-xl transition shadow-md whitespace-nowrap">
                Tìm kiếm
            </button>
        </form>
    </div>

    <!-- Danh sách khóa học -->
    <?php if (empty($courses)): ?>
        <div class="bg-white p-16 rounded-3xl text-center shadow-sm border border-gray-100">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-folder-open text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Không tìm thấy khóa học nào</h3>
            <p class="text-gray-500">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc để xem các khóa học khác nhé.</p>
            <a href="?action=courses" class="inline-block mt-6 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Xóa bộ lọc</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($courses as $course): ?>
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition duration-300 flex flex-col group">
                <div class="relative overflow-hidden">
                    <?php
                        $thumbnail = !empty($course['thumbnail'])
                            ? htmlspecialchars($course['thumbnail'])
                            : 'https://placehold.co/600x400/f59e0b/white?text=E-Learning';
                    ?>
                    <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($course['title']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                    <?php if (!empty($course['is_featured'])): ?>
                        <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5 uppercase tracking-wider">
                            <i class="fa-solid fa-fire text-yellow-300"></i> Nổi bật
                        </div>
                    <?php endif; ?>
                </div>
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-primary font-bold tracking-wider uppercase bg-yellow-50 px-2 py-1 rounded-md">
                                <?= htmlspecialchars($course['category_name'] ?? 'Khóa học') ?>
                            </span>
                            <span class="text-xs text-gray-500 font-medium"><i class="fa-regular fa-clock"></i> <?= date('d/m/Y', strtotime($course['created_at'])) ?></span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-primary transition"><?= htmlspecialchars($course['title']) ?></h3>
                        <div class="text-sm text-gray-500 mb-6 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars(strip_tags($course['description'])) ?>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-100 pt-5 mt-auto">
                        <span class="text-xl font-black <?= (isset($course['price']) && $course['price'] > 0) ? 'text-primary' : 'text-green-500' ?>">
                            <?= (isset($course['price']) && $course['price'] > 0) ? number_format($course['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                        </span>
                        <a href="?action=detail&id=<?= $course['id'] ?>" class="text-gray-500 hover:text-white border border-gray-200 hover:border-primary hover:bg-primary font-bold py-2 px-5 rounded-xl transition shadow-sm">Chi tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- PHÂN TRANG -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="mt-16 flex justify-center">
            <nav class="flex items-center gap-2">
                <?php 
                    $currentSearch = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                    $currentSort = isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
                    $currentCategory = isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '';
                    $queryParams = $currentSearch . $currentSort . $currentCategory;
                ?>
                
                <?php if (isset($page) && $page > 1): ?>
                    <a href="?action=courses&page=<?= $page - 1 ?><?= $queryParams ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded-full hover:bg-primary hover:text-white hover:border-primary transition shadow-sm">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?action=courses&page=<?= $i ?><?= $queryParams ?>" class="w-10 h-10 flex items-center justify-center <?= $i === ($page ?? 1) ? 'bg-primary text-white border-primary shadow-md' : 'bg-white border-gray-200 text-gray-600 hover:bg-primary hover:text-white hover:border-primary shadow-sm' ?> border rounded-full font-bold transition">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if (isset($page) && $page < $totalPages): ?>
                    <a href="?action=courses&page=<?= $page + 1 ?><?= $queryParams ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded-full hover:bg-primary hover:text-white hover:border-primary transition shadow-sm">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>
