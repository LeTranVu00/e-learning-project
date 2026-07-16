<?php
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Vừa xong';
        if ($diff < 3600) return floor($diff / 60) . ' phút trước';
        if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
        if ($diff < 2592000) return floor($diff / 86400) . ' ngày trước';
        if ($diff < 31536000) return floor($diff / 2592000) . ' tháng trước';
        return floor($diff / 31536000) . ' năm trước';
    }
}
?>
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <?php if (empty($isAdminMode)): ?>
    <a href="?action=forum" class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2 mb-6 w-fit">
        <i class="fa-solid fa-arrow-left"></i> Trở về Diễn đàn
    </a>
    <?php endif; ?>

    <!-- ===== Thẻ bài viết ===== -->
    <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden transition-all duration-300 hover:shadow-md" data-aos="fade-up">
        
        <div class="flex items-center gap-4 mb-6 border-b border-gray-100 dark:border-gray-700 pb-6">
            <?php 
                $postAvatar = !empty($post['author_avatar']) 
                    ? $post['author_avatar'] 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random';
            ?>
            <img src="<?= htmlspecialchars($postAvatar) ?>" referrerpolicy="no-referrer" class="w-14 h-14 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 shrink-0">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white"><?= htmlspecialchars($post['author_name']) ?></h3>
                    <?php if($post['author_role'] == 'admin'): ?>
                        <span class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-bold px-2 py-0.5 rounded uppercase"><i class="fa-solid fa-shield-halved mr-1"></i>Admin</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-400 dark:text-gray-500"><i class="fa-regular fa-clock mr-1"></i> <?= timeAgo($post['created_at']) ?></p>
            </div>
            
            <?php $canEditPost = ($post['user_id'] == $_SESSION['user_id'] || in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor'])); ?>
            <?php if($canEditPost): ?>
            <div class="ml-auto relative" x-data="{ openPostMenu: false }">
                <button @click="openPostMenu = !openPostMenu" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition focus:outline-none">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div x-show="openPostMenu" @click.away="openPostMenu = false" x-cloak 
                     class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-10 overflow-hidden"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <button type="button" @click="$dispatch('open-edit-post'); openPostMenu = false" 
                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:text-yellow-700 dark:hover:text-yellow-400 transition flex items-center gap-2">
                        <i class="fa-solid fa-pen w-4"></i> Sửa bài
                    </button>
                    <a href="#"
                       onclick="confirmDeletePost('?action=forum_delete_post&id=<?= $post['id'] ?><?= !empty($isAdminMode) ? '&admin=1' : '' ?>')" 
                       class="block w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-trash w-4"></i> Xóa bài
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4"><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
            <?= strip_tags($post['content'], '<p><strong><em><ul><ol><li><a><br><h2><h3><h4><blockquote><code><pre>') ?>
        </div>
    </div>

    <?php
    // Tính tổng số bình luận để hiển thị badge
    $totalComments = 0;
    foreach ($commentTree as $group) { $totalComments += count($group); }

    // Lấy avatar người dùng hiện tại
    $currentUserAvatar = !empty($_SESSION['user_avatar']) 
        ? $_SESSION['user_avatar'] 
        : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_name'] ?? 'User') . '&background=random';

    // Comment sort hiện tại (truyền từ controller)
    $csort = $comment_sort ?? 'latest';
    ?>

    <div id="comments" class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700" 
         x-data="{ replyToId: null, editCommentId: null, openDropdownId: null }" data-aos="fade-up">
        
        <!-- Header: Tiêu đề + bộ lọc sắp xếp bình luận -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fa-regular fa-comments text-primary"></i> 
                Bình luận
                <span id="comment-badge" class="text-sm font-semibold bg-primary/10 text-primary px-2.5 py-0.5 rounded-full <?= $totalComments > 0 ? '' : 'hidden' ?>"><?= $totalComments ?></span>
            </h3>

            <!-- Bộ lọc sắp xếp bình luận -->
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-xl p-1 text-xs font-semibold">
                <a href="?action=forum_detail&id=<?= $post['id'] ?>&csort=latest#comments"
                   class="px-3 py-1.5 rounded-lg transition-colors <?= $csort === 'latest' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' ?>">
                    <i class="fa-regular fa-clock mr-1"></i>Mới nhất
                </a>
                <a href="?action=forum_detail&id=<?= $post['id'] ?>&csort=popular#comments"
                   class="px-3 py-1.5 rounded-lg transition-colors <?= $csort === 'popular' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' ?>">
                    <i class="fa-solid fa-fire mr-1 text-orange-400"></i>Được thích nhất
                </a>
            </div>
        </div>

        <form action="?action=forum_store_comment<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST" id="main-comment-form" class="mb-8 flex gap-4 items-start">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <img src="<?= htmlspecialchars($currentUserAvatar) ?>" referrerpolicy="no-referrer" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 shrink-0">
            <div class="flex-1 min-w-0">
                <textarea id="main-comment-textarea" name="content" rows="2" maxlength="2000" 
                    placeholder="Viết bình luận của bạn... (tối đa 2000 ký tự)" 
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-primary/50 focus:border-primary transition resize-none text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"></textarea>
                <div class="flex justify-end mt-2">
                    <button id="main-comment-submit" type="submit" class="bg-primary hover:bg-yellow-600 text-white font-medium py-2 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm">Gửi bình luận</button>
                </div>
            </div>
        </form>

        <div id="comments-list" class="space-y-6">
            <?php 
            $getFlatReplies = function($parentId) use (&$getFlatReplies, $commentTree) {
                $replies = [];
                if (isset($commentTree[$parentId])) {
                    foreach ($commentTree[$parentId] as $child) {
                        $replies[] = $child;
                        $replies = array_merge($replies, $getFlatReplies($child['id']));
                    }
                }
                return $replies;
            };

            $commentsById = [];
            foreach ($commentTree as $group) {
                foreach ($group as $c) {
                    $commentsById[$c['id']] = $c;
                }
            }
            $isAdminMode = $isAdminMode ?? false;
            $renderSingleComment = function($comment, $isReply) use ($post, $currentUserAvatar, $csort, $isAdminMode, $commentsById) {
                $cmtAvatar = !empty($comment['author_avatar']) ? $comment['author_avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($comment['author_name']) . '&background=random';
                $isEdited  = !empty($comment['updated_at']) && $comment['updated_at'] !== $comment['created_at'];
                $canModify = isset($_SESSION['user_id']) && ($comment['user_id'] == $_SESSION['user_id'] || in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor']));
                $likeCount = (int)($comment['like_count'] ?? 0);
                $dislikeCount = (int)($comment['dislike_count'] ?? 0);
                $userReaction = $comment['user_reaction'] ?? null;
            ?>
                <div class="flex gap-3 items-start">
                    <img src="<?= htmlspecialchars($cmtAvatar) ?>" referrerpolicy="no-referrer" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 shrink-0 mt-1">
                    <div class="flex-1 min-w-0">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl px-4 py-2.5 max-w-full transition-all"
                             :class="editCommentId === <?= $comment['id'] ?> ? 'block w-full' : 'inline-block'">
                            <div class="flex items-center justify-between gap-3 mb-1 min-w-0">
                                <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                                    <span class="font-bold text-sm text-gray-900 dark:text-white truncate max-w-[120px]"><?= htmlspecialchars($comment['author_name']) ?></span>
                                    <?php if ($comment['author_role'] == 'admin'): ?>
                                        <i class="fa-solid fa-circle-check text-blue-500 text-xs shrink-0" title="Admin"></i>
                                    <?php endif; ?>
                                    <?php 
                                    if ($isReply && !empty($comment['parent_id'])) {
                                        $parentComment = $commentsById[$comment['parent_id']] ?? null;
                                        if ($parentComment && $parentComment['user_id'] != $comment['user_id']) {
                                            echo '<i class="fa-solid fa-caret-right text-gray-400 text-xs shrink-0"></i>';
                                            echo '<span class="text-xs font-semibold text-gray-500 dark:text-gray-400 truncate max-w-[120px]" title="' . htmlspecialchars($parentComment['author_name']) . '">' . htmlspecialchars($parentComment['author_name']) . '</span>';
                                        }
                                    }
                                    ?>
                                    <span class="text-[11px] text-gray-400 font-normal whitespace-nowrap ml-1"><?= timeAgo($comment['created_at']) ?></span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <?php if ($canModify): ?>
                                        <div class="relative">
                                            <button @click.prevent="openDropdownId = (openDropdownId === <?= $comment['id'] ?> ? null : <?= $comment['id'] ?>)" @click.outside="if (openDropdownId === <?= $comment['id'] ?>) openDropdownId = null" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-1 transition"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                            <div x-show="openDropdownId === <?= $comment['id'] ?>" x-cloak 
                                                 class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-600 py-1 z-50 overflow-hidden"
                                                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                                <button @click="editCommentId = <?= $comment['id'] ?>; openDropdownId = null" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-primary transition flex items-center gap-2">
                                                    <i class="fa-solid fa-pen w-4"></i> Sửa
                                                </button>
                                                <a href="#" onclick="confirmDeleteComment('?action=forum_delete_comment&id=<?= $comment['id'] ?><?= !empty($isAdminMode) ? '&admin=1' : '' ?>')" class="block w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition flex items-center gap-2">
                                                    <i class="fa-solid fa-trash w-4"></i> Xóa
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Nội dung bình luận -->
                            <div x-show="editCommentId !== <?= $comment['id'] ?>">
                                <div class="text-gray-700 dark:text-gray-300 text-sm break-words whitespace-pre-wrap prose dark:prose-invert max-w-none"><?= $comment['content'] ?></div>
                            </div>

                            <!-- Form sửa bình luận -->
                            <?php if ($canModify): ?>
                            <div x-show="editCommentId === <?= $comment['id'] ?>" x-cloak>
                                <form action="?action=forum_update_comment<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <textarea name="content" required rows="5" maxlength="2000"
                                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-primary/40 dark:border-gray-500 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition text-sm resize-none mt-1 text-gray-900 dark:text-white"
                                    ><?= htmlspecialchars($comment['content']) ?></textarea>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="text-xs font-semibold text-white bg-primary hover:bg-yellow-600 px-3 py-1.5 rounded-lg transition-all shadow-sm">Lưu</button>
                                        <button type="button" @click="editCommentId = null" class="text-xs font-semibold text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 px-3 py-1.5 rounded-lg transition-all">Hủy</button>
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Thanh hành động -->
                        <div class="flex items-center gap-1.5 mt-1.5 ml-2 text-xs font-medium">
                            <button class="react-btn flex items-center gap-1 transition-colors <?= $userReaction === 'like' ? 'text-green-500 font-bold' : 'text-gray-400 dark:text-gray-500 hover:text-green-500 dark:hover:text-green-400' ?>"
                                    data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" data-type="like" data-csort="<?= $csort ?>">
                                <i class="fa-<?= $userReaction === 'like' ? 'solid' : 'regular' ?> fa-thumbs-up"></i>
                                <span class="reaction-count w-4 text-left"><?= $likeCount > 0 ? $likeCount : '' ?></span>
                            </button>
                            <button class="react-btn flex items-center gap-1 transition-colors <?= $userReaction === 'dislike' ? 'text-red-500 font-bold' : 'text-gray-400 dark:text-gray-500 hover:text-red-400 dark:hover:text-red-400' ?>"
                                    data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" data-type="dislike" data-csort="<?= $csort ?>">
                                <i class="fa-<?= $userReaction === 'dislike' ? 'solid' : 'regular' ?> fa-thumbs-down"></i>
                                <span class="reaction-count w-4 text-left"><?= $dislikeCount > 0 ? $dislikeCount : '' ?></span>
                            </button>
                            <span class="text-gray-200 dark:text-gray-600">|</span>
                            <button @click="replyToId = (replyToId === <?= $comment['id'] ?> ? null : <?= $comment['id'] ?>)" class="text-gray-400 dark:text-gray-500 hover:text-primary dark:hover:text-primary transition cursor-pointer font-bold">
                                Trả lời
                            </button>
                        </div>

                        <!-- Form trả lời -->
                        <div x-show="replyToId === <?= $comment['id'] ?>" x-cloak class="mt-3 flex gap-3 items-start" 
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">                                
                            <img src="<?= htmlspecialchars($currentUserAvatar) ?>" referrerpolicy="no-referrer" class="w-6 h-6 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 shrink-0 mt-1">                                
                            <form action="?action=forum_store_comment<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST" class="reply-form flex-1 min-w-0" data-parent-id="<?= $comment['id'] ?>">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                                <textarea name="content" required rows="1" maxlength="2000"
                                    class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 transition text-sm resize-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400" 
                                    placeholder="Trả lời <?= htmlspecialchars($comment['author_name']) ?>..."></textarea>
                                <div class="flex justify-end gap-2 mt-2">
                                    <button type="button" @click="replyToId = null" class="px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">Hủy</button>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-primary hover:bg-yellow-600 rounded-lg shadow-sm btn-submit transition-transform hover:-translate-y-0.5">Gửi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php
            }; // End renderSingleComment

            if (empty($commentTree[0])) {
                echo '<p class="text-center text-gray-400 italic py-4">Chưa có bình luận nào. Hãy là người mở đầu!</p>';
            } else {
                foreach ($commentTree[0] as $rootComment) {
                    echo '<div class="mt-6 comment-item">';
                    
                    // Render Root Comment
                    $renderSingleComment($rootComment, false);
                    
                    // Lấy tất cả replies con
                    $replies = $getFlatReplies($rootComment['id']);
                    if (!empty($replies)) {
                        $totalReplies = count($replies);
                        echo '<div class="mt-3 ml-4 pl-6 relative replies-container" x-data="{ visibleReplies: 3 }">';
                        // Đã chuyển đường dọc vào từng item để cắt phần thừa
                        
                        $replyIndex = 0;
                        foreach ($replies as $reply) {
                            $displayStyle = $replyIndex >= 3 ? 'style="display: none;"' : '';
                            echo '<div class="relative pt-3 comment-item reply-item" x-show="visibleReplies > ' . $replyIndex . '" ' . $displayStyle . '>';
                            // Nhánh cong nối vào avatar của reply hiện tại
                            echo '<div class="absolute left-[-24px] top-0 w-6 h-8 border-l-2 border-b-2 border-gray-200 rounded-bl-xl z-0"></div>';
                            
                            // Đường dọc đi tiếp xuống dưới (nếu KHÔNG phải là item cuối cùng)
                            echo '<div class="absolute left-[-24px] top-8 bottom-0 w-[2px] bg-gray-200 z-0 js-vertical-tail" x-show="' . $replyIndex . ' < Math.min(' . ($totalReplies - 1) . ', visibleReplies - 1)"></div>';
                            
                            $renderSingleComment($reply, true);
                            
                            echo '</div>';
                            $replyIndex++;
                        }
                        
                        if ($totalReplies > 3) {
                            echo '<div class="mt-2 mb-1 flex items-center gap-4 relative z-10 load-more-btn-wrapper">';
                            echo '<button x-show="visibleReplies < ' . $totalReplies . '" @click="visibleReplies += 3" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center gap-1.5"><i class="fa-solid fa-reply rotate-180"></i> Xem thêm phản hồi</button>';
                            echo '<button x-cloak x-show="visibleReplies > 3" @click="visibleReplies = 3" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-up"></i> Ẩn bớt</button>';
                            echo '</div>';
                        }
                        
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
const CURRENT_USER = {
    id: <?= json_encode($_SESSION['user_id'] ?? null) ?>,
    name: <?= json_encode($_SESSION['user_name'] ?? 'User') ?>,
    role: <?= json_encode($_SESSION['user_role'] ?? 'user') ?>,
    avatar: <?= json_encode($currentUserAvatar ?? '') ?>
};
const POST_ID = <?= json_encode($post['id'] ?? null) ?>;

document.addEventListener('DOMContentLoaded', function () {
    // (Sự kiện like đã được gom vào hàm _bindReactEvents ở cuối script)

    /**
     * AJAX Đăng bình luận (Form chính)
     */
    const mainForm = document.getElementById('main-comment-form');
    if (mainForm) {
        mainForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const contentInput = document.getElementById('main-comment-textarea');
            const submitBtn = document.getElementById('main-comment-submit');
            
            if (!contentInput.value.trim()) return;

            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';

            const formData = new FormData(this);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('?action=forum_store_comment', {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                
                const data = await res.json();
                if (data.success) {
                    contentInput.value = '';
                    if (typeof showToast === 'function') showToast(data.message, 'success');
                    
                    // Xóa dòng "Chưa có bình luận" nếu có
                    const emptyMsg = document.querySelector('#comments-list > p');
                    if (emptyMsg) emptyMsg.remove();

                    // Tạo HTML comment mới và chèn lên đầu
                    const newCommentHtml = _buildCommentHtml(data.comment_id, formData.get('content'), null);
                    document.getElementById('comments-list').insertAdjacentHTML('afterbegin', newCommentHtml);
                    
                    // Cập nhật số đếm
                    const badge = document.getElementById('comment-badge');
                    if (badge) {
                        badge.textContent = parseInt(badge.textContent || 0) + 1;
                        badge.classList.remove('hidden');
                    }
                    
                    // Gắn lại sự kiện cho nút react mới
                    _bindReactEvents();
                } else {
                    if (typeof showToast === 'function') showToast(data.message || 'Lỗi', 'error');
                }
            } catch (err) {
                console.error(err);
                if (typeof showToast === 'function') showToast('Lỗi kết nối!', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi bình luận';
            }
        });
    }

    /**
     * AJAX Đăng trả lời (Reply forms)
     */
    document.querySelectorAll('.reply-form').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const contentInput = this.querySelector('textarea[name="content"]');
            const submitBtn = this.querySelector('.btn-submit');
            const parentId = this.dataset.parentId;
            
            if (!contentInput.value.trim()) return;

            submitBtn.disabled = true;
            submitBtn.textContent = '...';

            const formData = new FormData(this);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('?action=forum_store_comment', {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });
                
                const data = await res.json();
                if (data.success) {
                    contentInput.value = '';
                    if (typeof showToast === 'function') showToast(data.message, 'success');
                    
                    // Đóng form
                    this.querySelector('button[type="button"]').click();

                    // Tìm DOM cha (comment-item) đang click "Trả lời"
                    const parentItem = this.closest('.comment-item');
                    let repliesContainer;
                    
                    if (parentItem.classList.contains('reply-item')) {
                        // Nếu đang reply 1 reply khác, container chính là parentNode
                        repliesContainer = parentItem.parentNode;
                    } else {
                        // Nếu đang reply root comment, tìm container con
                        repliesContainer = parentItem.querySelector('.replies-container');
                        if (!repliesContainer) {
                            // Tạo container mới nếu chưa có
                            parentItem.insertAdjacentHTML('beforeend', `
                                <div class="mt-3 ml-4 pl-6 relative replies-container" x-data="{ visibleReplies: 3 }">
                                </div>
                            `);
                            repliesContainer = parentItem.querySelector('.replies-container');
                        }
                    }

                    const parentAuthorEl = parentItem.querySelector('.text-gray-900');
                    const parentAuthorName = parentAuthorEl ? parentAuthorEl.innerText : '';
                    
                    // Cập nhật đường dọc của item cuối cùng hiện tại thành hiện (show tail)
                    const existingReplies = repliesContainer.querySelectorAll('.reply-item');
                    if (existingReplies.length > 0) {
                        const lastReply = existingReplies[existingReplies.length - 1];
                        const lastTail = lastReply.querySelector('.js-vertical-tail');
                        if (lastTail) {
                            lastTail.classList.remove('hidden');
                            lastTail.style.display = 'block';
                            lastTail.removeAttribute('x-show');
                        }
                    }

                    const replyHtml = _buildCommentHtml(data.comment_id, formData.get('content'), parentId, parentAuthorName);
                    
                    // Thêm vào container (nếu có nút load more thì insert trước nút đó)
                    const loadMoreBtn = repliesContainer.querySelector('.load-more-btn-wrapper');
                    if (loadMoreBtn) {
                        loadMoreBtn.insertAdjacentHTML('beforebegin', replyHtml);
                    } else {
                        repliesContainer.insertAdjacentHTML('beforeend', replyHtml);
                    }
                    
                    // Cập nhật số đếm
                    const badge = document.getElementById('comment-badge');
                    if (badge) {
                        badge.textContent = parseInt(badge.textContent || 0) + 1;
                        badge.classList.remove('hidden');
                    }
                    
                    // Gắn lại sự kiện react
                    _bindReactEvents();
                } else {
                    if (typeof showToast === 'function') showToast(data.message || 'Lỗi', 'error');
                }
            } catch (err) {
                console.error(err);
                if (typeof showToast === 'function') showToast('Lỗi kết nối!', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi';
            }
        });
    });
});

/**
 * Cập nhật giao diện nút like/dislike sau khi nhận JSON từ server
 */
function _updateReactionUI(commentId, userReaction, likeCount, dislikeCount) {
    const likeBtn    = document.querySelector(`.react-btn[data-comment-id="${commentId}"][data-type="like"]`);
    const dislikeBtn = document.querySelector(`.react-btn[data-comment-id="${commentId}"][data-type="dislike"]`);

    if (likeBtn) {
        _applyReactState(likeBtn,    userReaction === 'like',    likeCount,    'fa-thumbs-up',   'text-green-500', 'hover:text-green-500');
    }
    if (dislikeBtn) {
        _applyReactState(dislikeBtn, userReaction === 'dislike', dislikeCount, 'fa-thumbs-down', 'text-red-500',   'hover:text-red-400');
    }
}

function _applyReactState(btn, isActive, count, iconClass, activeColor, hoverClass) {
    const icon    = btn.querySelector('i');
    const countEl = btn.querySelector('.reaction-count');

    // Cập nhật icon: solid khi active, regular khi không
    if (icon) {
        icon.classList.remove('fa-solid', 'fa-regular');
        icon.classList.add(isActive ? 'fa-solid' : 'fa-regular');
    }

    // Cập nhật màu và bold, dọn dẹp các class cũ (cả chế độ dark)
    btn.classList.remove('text-green-500', 'text-red-500', 'text-gray-400', 'dark:text-gray-500', 'font-bold',
                         'hover:text-green-500', 'hover:text-red-400', 'dark:hover:text-green-400', 'dark:hover:text-red-400');
                         
    if (isActive) {
        btn.classList.add(activeColor, 'font-bold');
    } else {
        btn.classList.add('text-gray-400', 'dark:text-gray-500', hoverClass);
        if (hoverClass === 'hover:text-green-500') btn.classList.add('dark:hover:text-green-400');
        if (hoverClass === 'hover:text-red-400') btn.classList.add('dark:hover:text-red-400');
    }

    // Cập nhật số lượng
    if (countEl) {
        countEl.textContent = count > 0 ? count : '';
    }
}

/**
 * Hàm binding sự kiện like cho các nút mới được chèn bằng AJAX
 */
function _bindReactEvents() {
    document.querySelectorAll('.react-btn:not(.bound)').forEach(function (btn) {
        btn.classList.add('bound'); // Đánh dấu đã bind
        btn.addEventListener('click', async function () {
            // (Copy logic click từ phần trên hoặc bóc tách ra hàm riêng)
            const commentId = this.dataset.commentId;
            const postId    = this.dataset.postId;
            const type      = this.dataset.type;
            
            this.style.opacity = '0.45';
            this.style.pointerEvents = 'none';
            const self = this;

            try {
                const res = await fetch(`?action=forum_like_comment&comment_id=${commentId}&post_id=${postId}&type=${type}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) throw new Error('Server error ' + res.status);
                
                const data = await res.json();
                if (data.success) {
                    _updateReactionUI(commentId, data.user_reaction, data.like_count, data.dislike_count);
                } else {
                    if (typeof showToast === 'function') showToast(data.message || 'Có lỗi xảy ra!', 'error');
                }
            } catch (err) {
                console.error('Like AJAX error:', err);
                if (typeof showToast === 'function') showToast('Có lỗi kết nối, vui lòng thử lại!', 'error');
            } finally {
                self.style.opacity = '';
                self.style.pointerEvents = '';
            }
        });
    });
}
// Chạy lần đầu
_bindReactEvents();

/**
 * Hàm tạo HTML cho 1 bình luận (vừa gửi thành công qua AJAX)
 */
function _buildCommentHtml(commentId, content, parentId, parentAuthorName = '') {
    const isReply = parentId !== null;
    const wrapperClass = isReply 
        ? 'relative pt-3 comment-item reply-item' 
        : 'mt-6 comment-item';
    const borderHtml = isReply 
        ? `<div class="absolute left-[-24px] top-0 w-6 h-8 border-l-2 border-b-2 border-gray-200 rounded-bl-xl z-0"></div>
           <div class="absolute left-[-24px] top-8 bottom-0 w-[2px] bg-gray-200 z-0 hidden js-vertical-tail"></div>` 
        : '';
    
    // Thoát HTML cơ bản
    const safeContent = content.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const timeNow = 'Vừa xong';

    let adminBadge = CURRENT_USER.role === 'admin' 
        ? '<i class="fa-solid fa-circle-check text-blue-500 text-xs shrink-0" title="Admin"></i>' : '';

    let arrowHtml = isReply && parentAuthorName
        ? `<i class="fa-solid fa-caret-right text-gray-400 text-[10px]"></i>
           <span class="font-bold text-sm text-gray-600 truncate">${parentAuthorName}</span>`
        : '';

    let dropdownHtml = `
        <div class="relative">
            <button @click.prevent="openDropdownId = (openDropdownId === ${commentId} ? null : ${commentId})" @click.outside="if (openDropdownId === ${commentId}) openDropdownId = null" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-1 transition">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div x-show="openDropdownId === ${commentId}" x-transition x-cloak class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-600 py-1 z-50 overflow-hidden">
                ${!isReply && (CURRENT_USER.role === 'admin' || CURRENT_USER.role === 'instructor') ? `
                <a href="?action=forum_pin_comment&id=${commentId}" class="block px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-primary transition">
                    <i class="fa-solid fa-thumbtack w-5 text-gray-400"></i>Ghim
                </a>` : ''}
                <button @click="editCommentId = ${commentId}; openDropdownId = null" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-primary transition">
                    <i class="fa-regular fa-pen-to-square w-5 text-gray-400"></i>Sửa
                </button>
                <a href="#" onclick="confirmDeleteComment('?action=forum_delete_comment&id=${commentId}')" class="block px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                    <i class="fa-regular fa-trash-can w-5 text-red-400"></i>Xóa
                </a>
            </div>
        </div>
    `;

    return `
    <div class="${wrapperClass}">
        ${borderHtml}
        <div class="flex gap-2 items-start">
            <img src="${CURRENT_USER.avatar}" referrerpolicy="no-referrer" class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600 shrink-0 relative z-10 bg-white dark:bg-gray-800 mt-1">
            <div class="flex-1 min-w-0">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl px-4 py-2.5 inline-block max-w-full">
                    <div class="flex items-center justify-between gap-3 mb-1 min-w-0">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="font-bold text-sm text-gray-900 dark:text-white truncate">${CURRENT_USER.name}</span>
                            ${arrowHtml}
                            ${adminBadge}
                            <span class="text-[11px] text-gray-400 font-normal whitespace-nowrap ml-1">${timeNow}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            ${dropdownHtml}
                        </div>
                    </div>
                    <div x-show="editCommentId !== ${commentId}">
                        <p class="text-gray-700 dark:text-gray-300 text-sm break-words whitespace-pre-wrap">${safeContent}</p>
                    </div>

                    <div x-show="editCommentId === ${commentId}" x-cloak>
                        <form action="?action=forum_update_comment" method="POST">
                            <input type="hidden" name="comment_id" value="${commentId}">
                            <textarea name="content" required rows="2" maxlength="2000"
                                class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 transition text-sm resize-none text-gray-900 dark:text-white mt-1"
                            >${safeContent}</textarea>
                            <div class="flex gap-2 mt-1.5">
                                <button type="submit" class="text-xs font-semibold text-white bg-primary hover:bg-yellow-600 px-3 py-1.5 rounded-lg shadow-sm transition-transform hover:-translate-y-0.5">Lưu</button>
                                <button type="button" @click="editCommentId = null" class="text-xs font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 px-3 py-1.5 rounded-lg transition-colors">Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="flex items-center gap-1.5 mt-1.5 ml-2 text-xs font-medium">
                    <button class="react-btn flex items-center gap-1 transition text-gray-400 hover:text-green-500"
                            data-comment-id="${commentId}" data-post-id="${POST_ID}" data-type="like">
                        <i class="fa-regular fa-thumbs-up"></i>
                        <span class="reaction-count w-4 text-left"></span>
                    </button>
                    <button class="react-btn flex items-center gap-1 transition text-gray-400 hover:text-red-400"
                            data-comment-id="${commentId}" data-post-id="${POST_ID}" data-type="dislike">
                        <i class="fa-regular fa-thumbs-down"></i>
                        <span class="reaction-count w-4 text-left"></span>
                    </button>
                    <span class="text-gray-300 dark:text-gray-600">|</span>
                    <button @click="replyToId = (replyToId === ${commentId} ? null : ${commentId})" 
                            class="text-gray-400 hover:text-primary transition cursor-pointer font-bold">
                        Trả lời
                    </button>
                </div>
            </div>
        </div>
    </div>`;
}
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const adminMode = urlParams.get('admin') || <?= !empty($isAdminMode) ? 'true' : 'false' ?>;
        const currentUrl = window.location.href;
        if(adminMode && currentUrl.includes('action=forum_detail')) {
            // Nếu bị chuyển hướng nhầm về forum_detail khi đang admin_mode, báo cho iframe thay đổi URL.
            // Điều này có thể xảy ra nếu controller không xử lý triệt để được redirect.
            // (Đã xử lý ở controller, nhưng để phòng hờ)
        }
    });
</script>

<!-- Edit Post Modal -->
<?php if(isset($canEditPost) && $canEditPost): ?>
<div x-data="{ showEditPostModal: false }" @open-edit-post.window="showEditPostModal = true">
    <div x-show="showEditPostModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak 
         class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm transition-opacity" @click="showEditPostModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditPostModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl z-50 flex flex-col max-h-[90vh]">
                
                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center shrink-0 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fa-solid fa-pen text-primary mr-2"></i>Sửa bài viết
                    </h3>
                    <button @click="showEditPostModal = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=forum_update_post<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST" id="formEditPostInDetail" class="space-y-4">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required 
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition text-gray-900 dark:text-white">
                        </div>
                        <div x-data="{ initEditor() {
                            if(!window.editorPostInitialized) {
                                ClassicEditor.create(document.querySelector('#editor_edit_post_detail'), {
                                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
                                }).then(editor => {
                                    window.editorPostInitialized = true;
                                }).catch(err => console.error(err));
                            }
                        } }" x-init="$watch('showEditPostModal', value => { if(value) setTimeout(() => initEditor(), 100); })">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nội dung chi tiết</label>
                            <textarea name="content" id="editor_edit_post_detail"><?= htmlspecialchars($post['content']) ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t dark:border-gray-700 flex justify-end gap-3 shrink-0 rounded-b-2xl">
                    <button @click="showEditPostModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 font-medium transition">Hủy</button>
                    <button type="submit" form="formEditPostInDetail" class="bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"><i class="fa-solid fa-check mr-1"></i> Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<style> 
    .ck-editor__editable_inline { min-height: 200px; } 
    .ck.ck-balloon-panel { z-index: 999999 !important; }
</style>
<script>
    function confirmDeletePost(url) {
        Swal.fire({
            title: 'Xóa bài viết?',
            text: 'Hành động này không thể hoàn tác!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xóa vĩnh viễn',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = url;
        });
    }
    function confirmDeleteComment(url) {
        Swal.fire({
            title: 'Xóa bình luận?',
            text: 'Bình luận sẽ bị xóa vĩnh viễn!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = url;
        });
    }
</script>