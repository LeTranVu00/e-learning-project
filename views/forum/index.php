<?php
if (!function_exists('timeAgo')) {
    function timeAgo($datetime)
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60)
            return 'Vừa xong';
        if ($diff < 3600)
            return floor($diff / 60) . ' phút trước';
        if ($diff < 86400)
            return floor($diff / 3600) . ' giờ trước';
        if ($diff < 2592000)
            return floor($diff / 86400) . ' ngày trước';
        if ($diff < 31536000)
            return floor($diff / 2592000) . ' tháng trước';
        return floor($diff / 31536000) . ' năm trước';
    }
}
?>
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{ 
         showPostModal: false, 
         showEditModal: false,
         editData: { id: '', title: '' },
         openEdit(post) {
             this.editData.id = post.id;
             this.editData.title = post.title;
             this.showEditModal = true;
             if(window.editorEditForum) { window.editorEditForum.setData(post.content || ''); }
         }
     }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6 gap-4"
        data-aos="fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <i class="fa-solid fa-comments text-primary mr-2"></i> Cộng đồng học tập
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Hỏi đáp, chia sẻ kiến thức và thảo luận cùng mọi
                người.</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <?php if (($_GET['filter'] ?? '') === 'my_posts'): ?>
                <a href="?action=forum"
                    class="flex-1 sm:flex-none text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-globe mr-1"></i> Tất cả bài
                </a>
            <?php else: ?>
                <a href="?action=forum&filter=my_posts"
                    class="flex-1 sm:flex-none text-center bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-blue-600 dark:text-blue-400 font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-sm border border-blue-100 dark:border-blue-800 hover:shadow-md">
                    <i class="fa-solid fa-user-pen mr-1"></i> Bài của tôi
                </a>
            <?php endif; ?>

            <button @click="showPostModal = true"
                class="flex-1 sm:flex-none bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 px-5 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Đăng bài
            </button>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="relative z-40 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6"
        data-aos="fade-up" data-aos-delay="50">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="action" value="forum">
            <?php if (($_GET['filter'] ?? '') === 'my_posts'): ?>
                <input type="hidden" name="filter" value="my_posts">
            <?php endif; ?>

            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass text-gray-400 dark:text-gray-500 absolute left-4 top-3.5"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Tìm kiếm bài viết..."
                    class="w-full pl-11 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-primary/50 transition text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
            </div>
            <div class="w-full md:w-48 relative" x-data="{ 
                open: false, 
                sort: '<?= htmlspecialchars($_GET['sort'] ?? 'latest') ?>',
                options: {
                    'latest': 'Mới nhất',
                    'oldest': 'Cũ nhất',
                    'popular': 'Tương tác cao'
                }
            }">
                <input type="hidden" name="sort" x-model="sort">

                <button type="button" @click="open = !open" @click.away="open = false"
                    class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 transition text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer shadow-sm hover:border-gray-300 dark:hover:border-gray-500">
                    <span x-text="options[sort]"></span>
                    <i class="fa-solid fa-chevron-down text-gray-400 transition-transform duration-200 text-xs"
                        :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 -translate-y-2 scale-95" x-cloak
                    class="absolute z-20 w-full mt-1.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden py-1">

                    <template x-for="(label, value) in options" :key="value">
                        <button type="button"
                            @click="sort = value; open = false; $nextTick(() => $el.closest('form').submit())"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium transition-colors"
                            :class="sort === value ? 'bg-primary/10 text-primary dark:bg-primary/10' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-primary'">
                            <span x-text="label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </form>
    </div>

    <!-- Posts List -->
    <div class="space-y-4">
        <?php if (empty($posts)): ?>
            <div class="bg-white dark:bg-gray-800 p-12 rounded-2xl text-center border border-gray-100 dark:border-gray-700 shadow-sm"
                data-aos="fade-up">
                <i class="fa-regular fa-comment-dots text-5xl mb-4 text-gray-300 dark:text-gray-600"></i>
                <p class="font-medium text-gray-700 dark:text-gray-300">Không tìm thấy bài thảo luận nào!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $index => $post): ?>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-lg dark:hover:shadow-gray-900/30 transition-all duration-300 group hover:-translate-y-1"
                    data-aos="fade-up" data-aos-delay="<?= ($index % 5) * 30 ?>">
                    <div class="flex items-start gap-4">
                        <?php
                        $listAvatar = !empty($post['author_avatar'])
                            ? $post['author_avatar']
                            : ($post['user_id'] == $_SESSION['user_id'] && !empty($_SESSION['user_avatar'])
                                ? $_SESSION['user_avatar']
                                : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random');
                        ?>
                        <img src="<?= htmlspecialchars($listAvatar) ?>" referrerpolicy="no-referrer"
                            class="w-12 h-12 rounded-xl object-cover border-2 border-gray-200 dark:border-gray-600 shadow-sm shrink-0">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <h3 class="font-bold text-gray-900 dark:text-white">
                                    <?= htmlspecialchars($post['author_name']) ?></h3>
                                <?php if ($post['author_role'] == 'admin'): ?>
                                    <span
                                        class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-bold px-2 py-0.5 rounded-full uppercase">
                                        <i class="fa-solid fa-shield-halved mr-1"></i>Admin
                                    </span>
                                <?php endif; ?>

                                <div class="ml-auto flex items-center gap-2">
                                    <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i> <?= timeAgo($post['created_at']) ?>
                                    </span>

                                    <?php if ($_SESSION['user_id'] == $post['user_id'] || $_SESSION['user_role'] === 'admin'): ?>
                                        <div x-data="{ openMenu: false }" class="relative">
                                            <button @click="openMenu = !openMenu" @click.away="openMenu = false"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>

                                            <div x-show="openMenu" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100" x-cloak
                                                class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 z-10 overflow-hidden py-1">
                                                <button
                                                    @click="openEdit(<?= htmlspecialchars(json_encode($post), ENT_QUOTES, 'UTF-8') ?>); openMenu = false"
                                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:text-yellow-700 dark:hover:text-yellow-400 flex items-center gap-2 transition">
                                                    <i class="fa-solid fa-pen w-4"></i> Sửa bài
                                                </button>
                                                <a href="#"
                                                    onclick="confirmDeletePost('?action=forum_delete_post&id=<?= $post['id'] ?>')"
                                                    class="block px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 transition">
                                                    <i class="fa-solid fa-trash-can w-4"></i> Xóa bài
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <a href="?action=forum_detail&id=<?= $post['id'] ?>"
                                class="text-lg font-bold text-gray-800 dark:text-white hover:text-primary dark:hover:text-primary transition block mb-2">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>

                            <div class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-4 leading-relaxed">
                                <?= strip_tags($post['content']) ?>
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="?action=forum_detail&id=<?= $post['id'] ?>"
                                    class="text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition text-sm font-medium flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-600 hover:border-primary/30">
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
        <div class="mt-10 flex justify-center" data-aos="fade-up">
            <nav class="flex items-center gap-2">
                <?php
                $currentSearch = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                $currentSort = isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '';
                $currentFilter = isset($_GET['filter']) ? '&filter=' . urlencode($_GET['filter']) : '';
                $queryParams = $currentSearch . $currentSort . $currentFilter;
                ?>

                <?php if (isset($page) && $page > 1): ?>
                    <a href="?action=forum&page=<?= $page - 1 ?><?= $queryParams ?>"
                        class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-full hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </a>
                <?php else: ?>
                    <span
                        class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-300 dark:text-gray-600 rounded-full cursor-not-allowed">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?action=forum&page=<?= $i ?><?= $queryParams ?>"
                        class="w-10 h-10 flex items-center justify-center rounded-full font-bold transition-all duration-300 shadow-sm hover:shadow-md
                          <?= $i === ($page ?? 1)
                              ? 'bg-primary text-white border-primary shadow-md scale-110'
                              : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white hover:border-primary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if (isset($page) && $page < $totalPages): ?>
                    <a href="?action=forum&page=<?= $page + 1 ?><?= $queryParams ?>"
                        class="w-10 h-10 flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 rounded-full hover:bg-primary hover:text-white hover:border-primary transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                <?php else: ?>
                    <span
                        class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-300 dark:text-gray-600 rounded-full cursor-not-allowed">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </span>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>

    <!-- CREATE POST MODAL -->
    <div x-show="showPostModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm transition-opacity"
            @click="showPostModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showPostModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl z-50 flex flex-col max-h-[90vh]">

                <div
                    class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center shrink-0 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fa-solid fa-pen-to-square text-primary mr-2"></i>Tạo bài thảo luận
                    </h3>
                    <button @click="showPostModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=forum_store_post" method="POST" id="formPostForum" class="space-y-4"
                        onsubmit="document.getElementById('btnSubmitPost').disabled=true; document.getElementById('btnSubmitPost').classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none'); document.getElementById('btnSubmitPost').innerHTML='<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Đang đăng...';">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tiêu đề bài
                                viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                placeholder="Nhập tiêu đề hấp dẫn...">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nội dung
                                chi tiết</label>
                            <textarea name="content" id="editor_forum"></textarea>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t dark:border-gray-700 flex justify-end gap-3 shrink-0 rounded-b-2xl">
                    <button @click="showPostModal = false"
                        class="px-6 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 font-medium transition">
                        Hủy
                    </button>
                    <button type="submit" form="formPostForum" id="btnSubmitPost"
                        class="bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Đăng bài
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT POST MODAL -->
    <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm transition-opacity"
            @click="showEditModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl z-50 flex flex-col max-h-[90vh]">

                <div
                    class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center shrink-0 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fa-solid fa-pen text-primary mr-2"></i>Sửa bài viết
                    </h3>
                    <button @click="showEditModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=forum_update_post" method="POST" id="formEditForum" class="space-y-4"
                        onsubmit="document.getElementById('btnSubmitEdit').disabled=true; document.getElementById('btnSubmitEdit').classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none'); document.getElementById('btnSubmitEdit').innerHTML='<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Đang lưu...';">
                        <input type="hidden" name="id" :value="editData.id">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tiêu đề bài
                                viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editData.title" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition text-gray-900 dark:text-white"
                                placeholder="Nhập tiêu đề...">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nội dung
                                chi tiết</label>
                            <textarea name="content" id="editor_edit_forum"></textarea>
                        </div>
                    </form>
                </div>

                <div
                    class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t dark:border-gray-700 flex justify-end gap-3 shrink-0 rounded-b-2xl">
                    <button @click="showEditModal = false"
                        class="px-6 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 font-medium transition">
                        Hủy
                    </button>
                    <button type="submit" form="formEditForum" id="btnSubmitEdit"
                        class="bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .ck-editor__editable_inline {
        min-height: 200px;
    }

    .ck.ck-balloon-panel {
        z-index: 99999 !important;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const config = {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
        };

        // Modal Đăng bài
        ClassicEditor.create(document.querySelector('#editor_forum'), config).catch(err => console.error(err));

        // Modal Sửa bài
        ClassicEditor.create(document.querySelector('#editor_edit_forum'), config)
            .then(editor => { window.editorEditForum = editor; })
            .catch(err => console.error(err));
    });

    // Xóa bài viết bằng Swal.fire thay vì native confirm()
    function confirmDeletePost(url) {
        Swal.fire({
            title: 'Xóa bài viết?',
            text: 'Bạn có chắc muốn xóa vĩnh viễn bài viết này? Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xóa vĩnh viễn',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>