<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8" 
     x-data="{ 
         showPostModal: false, 
         showEditModal: false,
         editData: { id: '', title: '' },
         openEdit(post) {
             this.editData.id = post.id;
             this.editData.title = post.title;
             this.showEditModal = true;
             // Bơm dữ liệu cũ vào CKEditor Sửa
             if(window.editorEditForum) { window.editorEditForum.setData(post.content || ''); }
         }
     }">
    
    <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fa-solid fa-comments text-primary mr-2"></i> Cộng đồng học tập</h1>
            <p class="text-gray-500 text-sm mt-1">Hỏi đáp, chia sẻ kiến thức và thảo luận cùng mọi người.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <?php if(($_GET['filter'] ?? '') === 'my_posts'): ?>
                <a href="?action=forum" class="flex-1 sm:flex-none text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                    <i class="fa-solid fa-globe mr-1"></i> Tất cả bài
                </a>
            <?php else: ?>
                <a href="?action=forum&filter=my_posts" class="flex-1 sm:flex-none text-center bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold py-2.5 px-5 rounded-xl transition shadow-sm border border-blue-100">
                    <i class="fa-solid fa-user-pen mr-1"></i> Bài của tôi
                </a>
            <?php endif; ?>

            <button @click="showPostModal = true" class="flex-1 sm:flex-none bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 px-5 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Đăng bài
            </button>
        </div>
    </div>
    
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="action" value="forum">           
            <?php if(($_GET['filter'] ?? '') === 'my_posts'): ?>
                <input type="hidden" name="filter" value="my_posts">
            <?php endif; ?>

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass text-gray-400 absolute left-4 top-3.5"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tìm kiếm bài viết..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-primary transition text-sm">
            </div>
            <div class="w-full md:w-48">
                <select name="sort" onchange="this.form.submit()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-primary transition text-sm font-medium text-gray-700 cursor-pointer">
                    <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                    <option value="popular" <?= ($_GET['sort'] ?? '') === 'popular' ? 'selected' : '' ?>>Tương tác cao</option>
                </select>
            </div>
            <!-- <button type="submit" class="bg-dark hover:bg-gray-800 text-white font-medium px-5 py-2.5 rounded-xl transition text-sm hidden md:block">Lọc</button> -->
        </form>
    </div>

    <div class="space-y-4">
        <?php if(empty($posts)): ?>
            <div class="bg-white p-12 rounded-2xl text-center text-gray-500 border border-gray-100 shadow-sm">
                <i class="fa-regular fa-comment-dots text-5xl mb-4 text-gray-300"></i>
                <p class="font-medium text-gray-700">Không tìm thấy bài thảo luận nào!</p>
            </div>
        <?php else: ?>
            <?php foreach($posts as $post): ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                    <div class="flex items-start gap-4">
                        <?php 
                            // Logic Fallback Avatar đồng bộ với trang Chi tiết
                            $listAvatar = !empty($post['author_avatar']) 
                                ? $post['author_avatar'] 
                                : ($post['user_id'] == $_SESSION['user_id'] && !empty($_SESSION['user_avatar']) 
                                    ? $_SESSION['user_avatar'] 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random');
                        ?>
                        <img src="<?= htmlspecialchars($listAvatar) ?>" class="w-12 h-12 rounded-full object-cover border border-gray-200 shrink-0">
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-900"><?= htmlspecialchars($post['author_name']) ?></h3>
                                <?php if($post['author_role'] == 'admin'): ?>
                                    <span class="text-[10px] bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded uppercase"><i class="fa-solid fa-shield-halved mr-1"></i>Admin</span>
                                <?php endif; ?>
                                
                                <div class="ml-auto flex items-center gap-2">
                                    <span class="text-xs text-gray-400"><i class="fa-regular fa-clock mr-1"></i> <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
                                    
                                    <?php if($_SESSION['user_id'] == $post['user_id'] || $_SESSION['user_role'] === 'admin'): ?>
                                        <div x-data="{ openMenu: false }" class="relative">
                                            <button @click="openMenu = !openMenu" @click.away="openMenu = false" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            
                                            <div x-show="openMenu" x-cloak style="display: none;" class="absolute right-0 mt-1 w-32 bg-white rounded-xl shadow-lg border border-gray-100 z-10 overflow-hidden py-1">
                                                <button @click="openEdit(<?= htmlspecialchars(json_encode($post), ENT_QUOTES, 'UTF-8') ?>); openMenu = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 flex items-center gap-2 transition">
                                                    <i class="fa-solid fa-pen w-4"></i> Sửa bài
                                                </button>
                                                <a href="?action=forum_delete_post&id=<?= $post['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn bài viết này?');" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 transition">
                                                    <i class="fa-solid fa-trash-can w-4"></i> Xóa bài
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <a href="?action=forum_detail&id=<?= $post['id'] ?>" class="text-lg font-bold text-gray-800 hover:text-primary transition block mb-2">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                            
                            <div class="text-gray-600 text-sm line-clamp-2 mb-4 prose prose-sm max-w-none">
                                <?= strip_tags($post['content']) ?>
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="?action=forum_detail&id=<?= $post['id'] ?>" class="text-gray-500 hover:text-primary transition text-sm font-medium flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                                    <i class="fa-regular fa-comment"></i> <?= $post['comment_count'] ?> Bình luận
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- PHÂN TRANG -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="mt-10 flex justify-center">
        <nav class="flex items-center gap-2">
            <?php 
                $currentSearch = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                $currentSort = isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
                $currentFilter = isset($_GET['filter']) ? '&filter=' . urlencode($_GET['filter']) : '';
                $queryParams = $currentSearch . $currentSort . $currentFilter;
            ?>
            
            <?php if (isset($page) && $page > 1): ?>
                <a href="?action=forum&page=<?= $page - 1 ?><?= $queryParams ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded-full hover:bg-primary hover:text-white hover:border-primary transition shadow-sm">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?action=forum&page=<?= $i ?><?= $queryParams ?>" class="w-10 h-10 flex items-center justify-center <?= $i === ($page ?? 1) ? 'bg-primary text-white border-primary shadow-md' : 'bg-white border-gray-200 text-gray-600 hover:bg-primary hover:text-white hover:border-primary shadow-sm' ?> border rounded-full font-bold transition">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if (isset($page) && $page < $totalPages): ?>
                <a href="?action=forum&page=<?= $page + 1 ?><?= $queryParams ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded-full hover:bg-primary hover:text-white hover:border-primary transition shadow-sm">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </a>
            <?php endif; ?>
        </nav>
    </div>
    <?php endif; ?>

    <div x-show="showPostModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showPostModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showPostModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPostModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-3xl z-50 flex flex-col max-h-[90vh]">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold">Tạo bài thảo luận</h3>
                    <button @click="showPostModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=forum_store_post" method="POST" id="formPostForum" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Nội dung chi tiết</label>
                            <textarea name="content" id="editor_forum"></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                    <button @click="showPostModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200">Hủy</button>
                    <button type="submit" form="formPostForum" class="bg-primary text-white font-bold py-2.5 px-6 rounded-xl">Đăng bài</button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showEditModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showEditModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showEditModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-3xl z-50 flex flex-col max-h-[90vh]">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold">Sửa bài viết</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=forum_update_post" method="POST" id="formEditForum" class="space-y-4">
                        <input type="hidden" name="id" :value="editData.id">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editData.title" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Nội dung chi tiết</label>
                            <textarea name="content" id="editor_edit_forum"></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                    <button @click="showEditModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200">Hủy</button>
                    <button type="submit" form="formEditForum" class="bg-primary text-white font-bold py-2.5 px-6 rounded-xl">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style> 
    .ck-editor__editable_inline { min-height: 200px; } 
    .ck.ck-balloon-panel { z-index: 99999 !important; } 
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const config = { toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ] };
        
        // Modal Đăng bài
        ClassicEditor.create(document.querySelector('#editor_forum'), config).catch(err => console.error(err));
        
        // Modal Sửa bài
        ClassicEditor.create(document.querySelector('#editor_edit_forum'), config)
            .then(editor => { window.editorEditForum = editor; })
            .catch(err => console.error(err));
    });
</script>