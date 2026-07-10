<?php $pageTitle = 'Quản lý Bình luận'; require_once 'layouts/header.php'; ?>
<div x-data="{ 
    showCommentsModal: false, 
    activePostId: null, 
    activePostTitle: '', 
    openComments(post) { 
        this.activePostId = post.id; 
        this.activePostTitle = post.title; 
        this.showCommentsModal = true; 
    } 
}">
            <!-- Toast Notifications -->
            <?php if (isset($_SESSION['success'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                     class="fixed top-20 right-6 z-50 bg-white border-l-4 border-green-500 shadow-xl rounded-lg p-4 flex items-center gap-3 min-w-[300px]">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-500 shrink-0">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-800">Thành công</h4>
                        <p class="text-sm text-gray-500"><?= $_SESSION['success'] ?></p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                     class="fixed top-20 right-6 z-50 bg-white border-l-4 border-red-500 shadow-xl rounded-lg p-4 flex items-center gap-3 min-w-[300px]">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-800">Lỗi</h4>
                        <p class="text-sm text-gray-500"><?= $_SESSION['error'] ?></p>
                    </div>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="flex justify-between items-end mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Quản lý Bình luận</h1>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="index.php" class="flex flex-wrap gap-4 items-end">
                    <input type="hidden" name="action" value="admin_manage_comments">
                    
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tìm kiếm</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tên tác giả, tiêu đề, nội dung..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ngày đăng</label>
                        <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm text-gray-700">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sắp xếp</label>
                        <select name="sort" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm font-medium text-gray-700">
                            <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                            <option value="popular" <?= ($_GET['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Nhiều tương tác nhất</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-xl transition text-sm">
                        <i class="fa-solid fa-filter mr-2"></i> Lọc
                    </button>
                    
                    <?php if(!empty($_GET['search']) || !empty($_GET['date']) || (!empty($_GET['sort']) && $_GET['sort'] !== 'latest')): ?>
                    <a href="?action=admin_manage_comments" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition text-sm">
                        Xóa lọc
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto"><table class="w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider">
                            <th class="p-4 font-semibold w-1/5">Tác giả</th>
                            <th class="p-4 font-semibold w-2/5">Tiêu đề bài viết</th>
                            <th class="p-4 font-semibold text-center">Bình luận</th>
                            <th class="p-4 font-semibold w-32">Ngày đăng</th>
                            <th class="p-4 font-semibold text-center w-32">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(!empty($posts)): foreach($posts as $post): ?>
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($post['author_avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name'])) ?>" class="w-8 h-8 rounded-full border border-gray-200">
                                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($post['author_name']) ?></div>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="text-sm font-medium text-gray-800 line-clamp-2" title="<?= htmlspecialchars($post['title']) ?>">
                                    <?= htmlspecialchars($post['title']) ?>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none <?= $post['comment_count'] > 0 ? 'text-blue-100 bg-blue-500' : 'text-gray-500 bg-gray-200' ?> rounded-full">
                                    <?= $post['comment_count'] ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openComments({ id: <?= $post['id'] ?>, title: `<?= htmlspecialchars(addslashes($post['title'])) ?>` })" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg text-primary bg-orange-50 hover:bg-primary hover:text-white transition shadow-sm" title="Quản lý bình luận">
                                        <i class="fa-solid fa-comments"></i> Xem
                                    </button>
                                    
                                    <a href="?action=admin_toggle_post_featured&id=<?= $post['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-full transition shadow-sm border <?= isset($post['is_featured']) && $post['is_featured'] ? 'bg-orange-50 text-orange-500 border-orange-200 hover:bg-orange-100' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-200 hover:text-gray-600' ?>" title="<?= isset($post['is_featured']) && $post['is_featured'] ? 'Bỏ nổi bật' : 'Đánh dấu nổi bật' ?>">
                                        <i class="fa-solid fa-star text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                    <p>Chưa có bài viết nào trên hệ thống.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table></div>
            </div>
    <!-- MODAL QUẢN LÝ BÌNH LUẬN BẰNG IFRAME -->
    <div x-show="showCommentsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-md transition-opacity"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             @click.away="showCommentsModal = false">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <h3 class="text-lg font-bold text-gray-800" x-text="activePostTitle"></h3>
                <button @click="showCommentsModal = false" class="text-gray-400 hover:text-red-500 transition bg-white w-8 h-8 rounded-full shadow-sm flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="flex-1 bg-gray-50 relative overflow-hidden">
                <template x-if="showCommentsModal">
                    <iframe :src="'?action=admin_post_comments&id=' + activePostId" class="w-full h-full border-0 absolute inset-0"></iframe>
                </template>
            </div>
        </div>
    </div>

    <script>
    window.addEventListener('message', function(event) {
        if (event.data.action === 'close_modal_and_reload') {
            window.location.reload();
        }
    });
    </script>
</div>
<?php require_once 'layouts/footer.php'; ?>
