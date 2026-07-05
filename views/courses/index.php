<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-10 text-center" data-aos="fade-up">
        <?php if (!empty($category_info)): ?>
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Danh mục</span>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4 mt-2"><?= htmlspecialchars($category_info['name']) ?></h1>
        <?php else: ?>
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Khóa học</span>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4 mt-2">Khám phá Khóa học</h1>
        <?php endif; ?>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Tìm kiếm và lựa chọn lộ trình học tập phù hợp nhất với mục tiêu của bạn. Hàng trăm khóa học chất lượng đang chờ đón.</p>
    </div>

    <!-- Bộ lọc & Tìm kiếm -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-10" data-aos="fade-up" data-aos-delay="100">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
            <input type="hidden" name="action" value="courses">
            
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass text-gray-400 dark:text-gray-500 absolute left-4 top-3.5"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                       placeholder="Tìm kiếm tên khóa học, giảng viên..." 
                       class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-primary/50 transition text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
            </div>
            
            <div class="w-full md:w-64 relative" x-data="{ open: false, sort: '<?= $_GET['sort'] ?? 'latest' ?>' }">
                <input type="hidden" name="sort" x-model="sort" x-init="$watch('sort', value => $el.form.submit())">
                
                <button type="button" @click="open = !open" @click.away="open = false" 
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 transition text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                    <span x-text="{
                        'latest': '📅 Mới nhất',
                        'oldest': '📜 Cũ nhất',
                        'price_low': '💰 Giá thấp → cao',
                        'price_high': '💎 Giá cao → thấp'
                    }[sort] || '📅 Mới nhất'"></span>
                    <i class="fa-solid fa-chevron-down text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2" 
                     x-transition:enter-end="opacity-100 transform scale-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-100" 
                     x-transition:leave-start="opacity-100 transform scale-100 translate-y-0" 
                     x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2" 
                     class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl py-2 overflow-hidden" 
                     style="display: none;">
                    
                    <button type="button" @click="sort = 'latest'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-700" :class="sort === 'latest' ? 'text-primary font-bold bg-primary/10 dark:bg-primary/20' : 'text-gray-700 dark:text-gray-300'">
                        📅 Mới nhất
                    </button>
                    <button type="button" @click="sort = 'oldest'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-700" :class="sort === 'oldest' ? 'text-primary font-bold bg-primary/10 dark:bg-primary/20' : 'text-gray-700 dark:text-gray-300'">
                        📜 Cũ nhất
                    </button>
                    <button type="button" @click="sort = 'price_low'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-700" :class="sort === 'price_low' ? 'text-primary font-bold bg-primary/10 dark:bg-primary/20' : 'text-gray-700 dark:text-gray-300'">
                        💰 Giá thấp → cao
                    </button>
                    <button type="button" @click="sort = 'price_high'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-700" :class="sort === 'price_high' ? 'text-primary font-bold bg-primary/10 dark:bg-primary/20' : 'text-gray-700 dark:text-gray-300'">
                        💎 Giá cao → thấp
                    </button>
                </div>
            </div>
            
            <button type="submit" class="bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 whitespace-nowrap flex items-center justify-center gap-2">
                <i class="fa-solid fa-magnifying-glass text-sm"></i> Tìm kiếm
            </button>
        </form>
    </div>

    <!-- Danh sách khóa học -->
    <?php if (empty($courses)): ?>
        <div class="bg-white dark:bg-gray-800 p-16 rounded-3xl text-center shadow-sm border border-gray-100 dark:border-gray-700" data-aos="fade-up">
            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-folder-open text-4xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Không tìm thấy khóa học nào</h3>
            <p class="text-gray-500 dark:text-gray-400">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc để xem các khóa học khác nhé.</p>
            <a href="?action=courses" class="inline-flex items-center gap-2 mt-6 px-6 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition">
                <i class="fa-solid fa-rotate-left text-sm"></i> Xóa bộ lọc
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($courses as $index => $course): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300 flex flex-col group hover:-translate-y-2" 
                 data-aos="fade-up" 
                 data-aos-delay="<?= ($index % 6) * 50 ?>">
                <div class="relative overflow-hidden">
                    <?php
                        $thumbnail = !empty($course['thumbnail'])
                            ? htmlspecialchars($course['thumbnail'])
                            : 'https://placehold.co/600x400/f59e0b/white?text=E-Learning';
                    ?>
                    <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($course['title']) ?>" 
                         class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <?php if (!empty($course['is_featured'])): ?>
                        <div class="absolute top-3 left-3 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                            <i class="fa-solid fa-fire text-yellow-300"></i> Nổi bật
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute top-3 right-3 bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm rounded-full px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                        <i class="fa-solid fa-users mr-1"></i> 2.5k
                    </div>
                </div>
                
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-primary font-bold bg-primary/5 dark:bg-primary/10 px-3 py-1 rounded-full">
                                <?= htmlspecialchars($course['category_name'] ?? 'Khóa học') ?>
                            </span>
                            <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                <i class="fa-regular fa-clock"></i> <?= date('d/m/Y', strtotime($course['created_at'])) ?>
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                            <?= htmlspecialchars($course['title']) ?>
                        </h3>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars(strip_tags($course['description'])) ?>
                        </p>
                        
                        <!-- Rating Stars -->
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex text-yellow-400 text-sm">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">(4.8)</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-5 mt-auto">
                        <div class="flex flex-col">
                            <span class="text-xl font-bold <?= (isset($course['price']) && $course['price'] > 0) ? 'text-primary' : 'text-green-500' ?>">
                                <?= (isset($course['price']) && $course['price'] > 0) ? number_format($course['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                            </span>
                            <?php if (!empty($course['original_price']) && $course['original_price'] > ($course['price'] ?? 0)): ?>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <del class="text-sm text-gray-400 dark:text-gray-500"><?= number_format($course['original_price'], 0, ',', '.') ?>đ</del>
                                    <span class="text-xs text-red-500 font-semibold bg-red-50 dark:bg-red-900/30 px-2 py-0.5 rounded">
                                        -<?= round((1 - ($course['price'] ?? 0) / $course['original_price']) * 100) ?>%
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a href="?action=detail&id=<?= $course['id'] ?>" 
                           class="bg-primary/10 dark:bg-primary/20 text-primary hover:bg-primary hover:text-white font-semibold py-2.5 px-5 rounded-xl transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5">
                            Chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- PHÂN TRANG -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="mt-16 flex justify-center" data-aos="fade-up">
            <nav class="flex items-center gap-2">
                <?php 
                    $currentSearch = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                    $currentSort = isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
                    $currentCategory = isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '';
                    $queryParams = $currentSearch . $currentSort . $currentCategory;
                ?>
                
                <?php if (isset($page) && $page > 1): ?>
                    <a href="?action=courses&page=<?= $page - 1 ?><?= $queryParams ?>" 
                       class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-full hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </a>
                <?php else: ?>
                    <span class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-300 dark:text-gray-600 rounded-full cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </span>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?action=courses&page=<?= $i ?><?= $queryParams ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-full font-bold transition-all duration-300 shadow-sm hover:shadow-md
                              <?= $i === ($page ?? 1) 
                                  ? 'bg-primary text-white border-primary shadow-md scale-110' 
                                  : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if (isset($page) && $page < $totalPages): ?>
                    <a href="?action=courses&page=<?= $page + 1 ?><?= $queryParams ?>" 
                       class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-full hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                <?php else: ?>
                    <span class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-300 dark:text-gray-600 rounded-full cursor-not-allowed">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </span>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>