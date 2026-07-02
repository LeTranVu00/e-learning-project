<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <?php if (empty($isAdminMode)): ?>
    <a href="?action=forum" class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2 mb-6 w-fit">
        <i class="fa-solid fa-arrow-left"></i> Trở về Diễn đàn
    </a>
    <?php endif; ?>

    <!-- ===== Thẻ bài viết ===== -->
    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
        
        <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-6">
            <?php 
                $postAvatar = !empty($post['author_avatar']) 
                    ? $post['author_avatar'] 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random';
            ?>
            <img src="<?= htmlspecialchars($postAvatar) ?>" class="w-14 h-14 rounded-full object-cover border border-gray-200 shrink-0">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-lg text-gray-900"><?= htmlspecialchars($post['author_name']) ?></h3>
                    <?php if($post['author_role'] == 'admin'): ?>
                        <span class="text-[10px] bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded uppercase"><i class="fa-solid fa-shield-halved mr-1"></i>Admin</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-gray-400"><i class="fa-regular fa-clock mr-1"></i> <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></p>
            </div>
            
            <?php $canEditPost = ($post['user_id'] == $_SESSION['user_id'] || in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor'])); ?>
            <?php if($canEditPost): ?>
            <div class="ml-auto relative" x-data="{ openPostMenu: false }">
                <button @click="openPostMenu = !openPostMenu" class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition focus:outline-none">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <div x-show="openPostMenu" @click.away="openPostMenu = false" x-cloak class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-10"
                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95">
                    <button type="button" @click="$dispatch('open-edit-post'); openPostMenu = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition flex items-center gap-2">
                        <i class="fa-solid fa-pen w-4"></i> Sửa bài
                    </button>
                    <a href="?action=forum_delete_post&id=<?= $post['id'] ?><?= !empty($isAdminMode) ? '&admin=1' : '' ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                        <i class="fa-solid fa-trash w-4"></i> Xóa bài
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="prose max-w-none text-gray-700">
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

    <div id="comments" class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100" 
         x-data="{ replyToId: null, editCommentId: null, openDropdownId: null }">
        
        <!-- Header: Tiêu đề + bộ lọc sắp xếp bình luận -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 border-b border-gray-100 pb-4">
            <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fa-regular fa-comments text-primary"></i> 
                Bình luận
                <span id="comment-badge" class="text-sm font-semibold bg-primary/10 text-primary px-2.5 py-0.5 rounded-full <?= $totalComments > 0 ? '' : 'hidden' ?>"><?= $totalComments ?></span>
            </h3>

            <!-- Bộ lọc sắp xếp bình luận -->
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 text-xs font-semibold">
                <a href="?action=forum_detail&id=<?= $post['id'] ?>&csort=latest#comments"
                   class="px-3 py-1.5 rounded-lg transition <?= $csort === 'latest' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i class="fa-regular fa-clock mr-1"></i>Mới nhất
                </a>
                <a href="?action=forum_detail&id=<?= $post['id'] ?>&csort=popular#comments"
                   class="px-3 py-1.5 rounded-lg transition <?= $csort === 'popular' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i class="fa-solid fa-fire mr-1 text-orange-400"></i>Được thích nhất
                </a>
            </div>
        </div>

        <form action="?action=forum_store_comment<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST" id="main-comment-form" class="mb-8 flex gap-4 items-start">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            <img src="<?= htmlspecialchars($currentUserAvatar) ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0">
            <div class="flex-1 min-w-0">
                <textarea id="main-comment-textarea" name="content" rows="2" maxlength="2000" 
                    placeholder="Viết bình luận của bạn... (tối đa 2000 ký tự)" 
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-primary transition resize-none text-sm"></textarea>
                <div class="flex justify-end mt-2">
                    <button id="main-comment-submit" type="submit" class="bg-dark hover:bg-gray-800 text-white font-medium py-2 px-6 rounded-xl transition text-sm">Gửi bình luận</button>
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

            $renderSingleComment = function($comment, $isReply) use ($post, $currentUserAvatar, $csort, $isAdminMode) {
                $cmtAvatar = !empty($comment['author_avatar']) ? $comment['author_avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($comment['author_name']) . '&background=random';
                $isEdited  = !empty($comment['updated_at']) && $comment['updated_at'] !== $comment['created_at'];
                $canModify = isset($_SESSION['user_id']) && ($comment['user_id'] == $_SESSION['user_id'] || in_array($_SESSION['user_role'] ?? '', ['admin', 'instructor']));
                $likeCount = (int)($comment['like_count'] ?? 0);
                $dislikeCount = (int)($comment['dislike_count'] ?? 0);
                $userReaction = $comment['user_reaction'] ?? null;
            ?>
                <div class="flex gap-2 items-start">
                    <img src="<?= htmlspecialchars($cmtAvatar) ?>" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0 mt-1">
                    <div class="flex-1 min-w-0">
                        <div class="bg-gray-50 rounded-2xl px-4 py-2.5 inline-block max-w-full">
                            <div class="flex items-center justify-between gap-3 mb-1">
                                <span class="font-bold text-sm text-gray-900"><?= htmlspecialchars($comment['author_name']) ?></span>
                                <?php if ($canModify): ?>
                                    <div class="relative">
                                        <button @click.prevent="openDropdownId = (openDropdownId === <?= $comment['id'] ?> ? null : <?= $comment['id'] ?>)" class="text-gray-400 hover:text-gray-700 px-1"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        <div x-show="openDropdownId === <?= $comment['id'] ?>" x-cloak class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                                            <button @click.prevent="showToast('Sửa bình luận (Chỉ hỗ trợ dạng popup trong tương lai)')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary transition flex items-center gap-2">
                                                <i class="fa-solid fa-pen w-4"></i> Sửa
                                            </button>
                                            <a href="?action=forum_delete_comment&id=<?= $comment['id'] ?><?= !empty($isAdminMode) ? '&admin=1' : '' ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2">
                                                <i class="fa-solid fa-trash w-4"></i> Xóa
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Nội dung bình luận -->
                            <div x-show="editCommentId !== <?= $comment['id'] ?>">
                                <p class="text-gray-700 text-sm break-words whitespace-pre-wrap"><?= htmlspecialchars($comment['content']) ?></p>
                            </div>

                            <!-- Form sửa bình luận -->
                            <?php if ($canModify): ?>
                            <div x-show="editCommentId === <?= $comment['id'] ?>" x-cloak>
                                <form action="?action=forum_update_comment<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <textarea name="content" required rows="2" maxlength="2000"
                                        class="w-full px-3 py-2 bg-white border border-primary/40 rounded-xl outline-none focus:ring-2 focus:ring-primary transition text-sm resize-none mt-1"
                                    ><?= htmlspecialchars($comment['content']) ?></textarea>
                                    <div class="flex gap-2 mt-1.5">
                                        <button type="submit" class="text-xs font-semibold text-white bg-primary hover:bg-yellow-600 px-3 py-1.5 rounded-lg transition">Lưu</button>
                                        <button type="button" @click="editCommentId = null" class="text-xs font-semibold text-gray-500 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition">Hủy</button>
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Thanh hành động -->
                        <div class="flex items-center gap-1.5 mt-1.5 ml-2 text-xs font-medium">
                            <button class="react-btn flex items-center gap-1 transition <?= $userReaction === 'like' ? 'text-green-500 font-bold' : 'text-gray-400 hover:text-green-500' ?>"
                                    data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" data-type="like" data-csort="<?= $csort ?>">
                                <i class="fa-<?= $userReaction === 'like' ? 'solid' : 'regular' ?> fa-thumbs-up"></i>
                                <span class="reaction-count w-4 text-left"><?= $likeCount > 0 ? $likeCount : '' ?></span>
                            </button>
                            <button class="react-btn flex items-center gap-1 transition <?= $userReaction === 'dislike' ? 'text-red-500 font-bold' : 'text-gray-400 hover:text-red-400' ?>"
                                    data-comment-id="<?= $comment['id'] ?>" data-post-id="<?= $post['id'] ?>" data-type="dislike" data-csort="<?= $csort ?>">
                                <i class="fa-<?= $userReaction === 'dislike' ? 'solid' : 'regular' ?> fa-thumbs-down"></i>
                                <span class="reaction-count w-4 text-left"><?= $dislikeCount > 0 ? $dislikeCount : '' ?></span>
                            </button>
                            <span class="text-gray-200">|</span>
                            <button @click="replyToId = (replyToId === <?= $comment['id'] ?> ? null : <?= $comment['id'] ?>)" class="text-gray-400 hover:text-primary transition cursor-pointer font-bold">
                                Trả lời
                            </button>
                        </div>

                        <!-- Form trả lời -->
                        <div x-show="replyToId === <?= $comment['id'] ?>" x-cloak class="mt-3 flex gap-3 items-start" 
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">                                
                            <img src="<?= htmlspecialchars($currentUserAvatar) ?>" class="w-6 h-6 rounded-full object-cover border border-gray-200 shrink-0 mt-1">                                
                            <form action="?action=forum_store_comment<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST" class="reply-form flex-1 min-w-0" data-parent-id="<?= $comment['id'] ?>">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                                <textarea name="content" required rows="1" maxlength="2000"
                                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary transition text-sm resize-none" 
                                    placeholder="Trả lời <?= htmlspecialchars($comment['author_name']) ?>..."></textarea>
                                <div class="flex justify-end gap-2 mt-2">
                                    <button type="button" @click="replyToId = null" class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100 rounded-lg">Hủy</button>
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-primary hover:bg-yellow-600 rounded-lg shadow-sm btn-submit">Gửi</button>
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
                        echo '<div class="mt-3 ml-[20px] pl-6 relative replies-container" x-data="{ visibleReplies: 3 }">';
                        // Đường thẳng dọc nối tất cả replies
                        echo '<div class="absolute left-0 top-0 bottom-6 w-[2px] bg-gray-200 z-0"></div>';
                        
                        $replyIndex = 0;
                        foreach ($replies as $reply) {
                            $displayStyle = $replyIndex >= 3 ? 'style="display: none;"' : '';
                            echo '<div class="relative pt-3 comment-item reply-item" x-show="visibleReplies > ' . $replyIndex . '" ' . $displayStyle . '>';
                            // Đường thẳng ngang nối vào reply
                            echo '<div class="absolute left-[-24px] top-8 w-6 h-[2px] bg-gray-200 z-0"></div>';
                            
                            $renderSingleComment($reply, true);
                            
                            echo '</div>';
                            $replyIndex++;
                        }
                        
                        if ($totalReplies > 3) {
                            echo '<div class="mt-2 mb-1 relative z-10 load-more-btn-wrapper" x-show="visibleReplies < ' . $totalReplies . '">';
                            echo '<button @click="visibleReplies += 3" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center gap-1.5"><i class="fa-solid fa-reply rotate-180"></i> Xem thêm phản hồi</button>';
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
                const res = await fetch('?action=forum_store_comment', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
                const res = await fetch('?action=forum_store_comment', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
                                <div class="mt-3 ml-[20px] pl-6 relative replies-container" x-data="{ visibleReplies: 3 }">
                                    <div class="absolute left-0 top-0 bottom-6 w-[2px] bg-gray-200 z-0"></div>
                                </div>
                            `);
                            repliesContainer = parentItem.querySelector('.replies-container');
                        }
                    }

                    const parentAuthorEl = parentItem.querySelector('.text-gray-900');
                    const parentAuthorName = parentAuthorEl ? parentAuthorEl.innerText : '';
                    
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

    // Cập nhật màu và bold
    btn.classList.remove('text-green-500', 'text-red-500', 'text-gray-400', 'font-bold',
                         'hover:text-green-500', 'hover:text-red-400');
    if (isActive) {
        btn.classList.add(activeColor, 'font-bold');
    } else {
        btn.classList.add('text-gray-400', hoverClass);
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
        ? `<div class="absolute left-[-24px] top-8 w-6 h-[2px] bg-gray-200 z-0"></div>` 
        : '';
    
    // Thoát HTML cơ bản
    const safeContent = content.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const timeNow = new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) + ' ' + 
                    new Date().toLocaleDateString('vi-VN', {day: '2-digit', month: '2-digit', year: 'numeric'});

    let adminBadge = CURRENT_USER.role === 'admin' 
        ? '<i class="fa-solid fa-circle-check text-blue-500 text-xs shrink-0" title="Admin"></i>' : '';

    let arrowHtml = isReply && parentAuthorName
        ? `<i class="fa-solid fa-caret-right text-gray-400 text-[10px]"></i>
           <span class="font-bold text-sm text-gray-600 truncate">${parentAuthorName}</span>`
        : '';

    let dropdownHtml = `
        <div class="relative">
            <button @click.prevent="openDropdownId = (openDropdownId === ${commentId} ? null : ${commentId})" @click.outside="if (openDropdownId === ${commentId}) openDropdownId = null" class="text-gray-400 hover:text-gray-700 px-1">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div x-show="openDropdownId === ${commentId}" x-transition x-cloak class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                ${!isReply && (CURRENT_USER.role === 'admin' || CURRENT_USER.role === 'instructor') ? `
                <a href="?action=forum_pin_comment&id=${commentId}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-thumbtack w-5 text-gray-400"></i>Ghim
                </a>` : ''}
                <button @click="editCommentId = ${commentId}; openDropdownId = null" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-regular fa-pen-to-square w-5 text-gray-400"></i>Sửa
                </button>
                <a href="?action=forum_delete_comment&id=${commentId}" onclick="return confirm('Bạn có chắc muốn xóa bình luận này?')" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                    <i class="fa-regular fa-trash-can w-5 text-red-400"></i>Xóa
                </a>
            </div>
        </div>
    `;

    return \`
    <div class="\${wrapperClass}">
        \${borderHtml}
        <div class="flex gap-2 items-start">
            <img src="\${CURRENT_USER.avatar}" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0 relative z-10 bg-white mt-1">
            <div class="flex-1 min-w-0">
                <div class="bg-gray-50 rounded-2xl px-4 py-2.5 inline-block max-w-full">
                    <div class="flex items-center justify-between gap-3 mb-1 min-w-0">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="font-bold text-sm text-gray-900 truncate">\${CURRENT_USER.name}</span>
                            \${arrowHtml}
                            \${adminBadge}
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[11px] text-gray-400 font-normal whitespace-nowrap">\${timeNow}</span>
                            \${dropdownHtml}
                        </div>
                    </div>
                    <div x-show="editCommentId !== \${commentId}">
                        <p class="text-gray-700 text-sm break-words whitespace-pre-wrap">\${safeContent}</p>
                    </div>

                    <div x-show="editCommentId === \${commentId}" x-cloak>
                        <form action="?action=forum_update_comment" method="POST">
                            <input type="hidden" name="comment_id" value="\${commentId}">
                            <textarea name="content" required rows="2" maxlength="2000"
                                class="w-full px-3 py-2 bg-white border border-primary/40 rounded-xl outline-none focus:ring-2 focus:ring-primary transition text-sm resize-none mt-1"
                            >\${safeContent}</textarea>
                            <div class="flex gap-2 mt-1.5">
                                <button type="submit" class="text-xs font-semibold text-white bg-primary hover:bg-yellow-600 px-3 py-1.5 rounded-lg transition">Lưu</button>
                                <button type="button" @click="editCommentId = null" class="text-xs font-semibold text-gray-500 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition">Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="flex items-center gap-1.5 mt-1.5 ml-2 text-xs font-medium">
                    <button class="react-btn flex items-center gap-1 transition text-gray-400 hover:text-green-500"
                            data-comment-id="\${commentId}" data-post-id="\${POST_ID}" data-type="like">
                        <i class="fa-regular fa-thumbs-up"></i>
                        <span class="reaction-count w-4 text-left"></span>
                    </button>
                    <button class="react-btn flex items-center gap-1 transition text-gray-400 hover:text-red-400"
                            data-comment-id="\${commentId}" data-post-id="\${POST_ID}" data-type="dislike">
                        <i class="fa-regular fa-thumbs-down"></i>
                        <span class="reaction-count w-4 text-left"></span>
                    </button>
                    <span class="text-gray-200">|</span>
                    <button @click="replyToId = (replyToId === \${commentId} ? null : \${commentId})" 
                            class="text-gray-400 hover:text-primary transition cursor-pointer font-bold">
                        Trả lời
                    </button>
                </div>
            </div>
        </div>
    </div>\`;
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
    <div x-show="showEditPostModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto">
        <div x-show="showEditPostModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showEditPostModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showEditPostModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-3xl z-50 flex flex-col max-h-[90vh]">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold">Sửa bài viết</h3>
                    <button @click="showEditPostModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=forum_update_post<?= !empty($isAdminMode) ? '&admin=1' : '' ?>" method="POST" id="formEditPostInDetail" class="space-y-4">
                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Tiêu đề bài viết <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
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
                            <label class="block text-sm font-semibold mb-2">Nội dung chi tiết</label>
                            <textarea name="content" id="editor_edit_post_detail"><?= htmlspecialchars($post['content']) ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                    <button @click="showEditPostModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200">Hủy</button>
                    <button type="submit" form="formEditPostInDetail" class="bg-primary text-white font-bold py-2.5 px-6 rounded-xl">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Load CKEditor if not loaded -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style> 
    .ck-editor__editable_inline { min-height: 200px; } 
    .ck.ck-balloon-panel { z-index: 999999 !important; } 
</style>
<?php endif; ?>