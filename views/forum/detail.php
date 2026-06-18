<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <a href="?action=forum" class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2 mb-6 w-fit">
        <i class="fa-solid fa-arrow-left"></i> Trở về Diễn đàn
    </a>

    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
        
        <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-6">
            <?php 
                $postAvatar = !empty($post['author_avatar']) 
                    ? $post['author_avatar'] 
                    : ($post['user_id'] == $_SESSION['user_id'] && !empty($_SESSION['user_avatar']) 
                        ? $_SESSION['user_avatar'] 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name']) . '&background=random');
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
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($post['title']) ?></h1>
        
        <div class="prose max-w-none text-gray-700">
            <?= $post['content'] ?>
        </div>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-gray-100" x-data="{ replyToId: null }">
        <h3 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">
            <i class="fa-regular fa-comments text-primary mr-2"></i> Bình luận
        </h3>

        <form action="?action=forum_store_comment" method="POST" class="mb-8 flex gap-4 items-start">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            
            <?php 
                $currentUserAvatar = !empty($_SESSION['user_avatar']) 
                    ? $_SESSION['user_avatar'] 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['fullname'] ?? 'User') . '&background=random';
            ?>
            <img src="<?= htmlspecialchars($currentUserAvatar) ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0">
            <div class="flex-1 min-w-0">

                <textarea name="content" required rows="2" placeholder="Viết bình luận của bạn..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:bg-white focus:ring-2 focus:ring-primary transition resize-none"></textarea>
                <div class="flex justify-end mt-2">
                    <button type="submit" class="bg-dark hover:bg-gray-800 text-white font-medium py-2 px-6 rounded-xl transition text-sm">Gửi bình luận</button>
                </div>
            </div>
        </form>

        <div class="space-y-6">
            <?php 
            $renderComments = function($parentId = 0, $level = 0) use (&$renderComments, $commentTree, $post) {
                if (!isset($commentTree[$parentId])) return;
                
                foreach ($commentTree[$parentId] as $comment): 
                    $wrapperClass = 'mt-6';
                    if ($level === 1) {
                        $wrapperClass = 'mt-4 ml-6 md:ml-12 border-l-2 border-gray-100 pl-4 md:pl-6 relative';
                    } elseif ($level > 1) {
                        $wrapperClass = 'mt-4 relative'; 
                    }
            ?>
                <div class="<?= $wrapperClass ?>">
                    
                    <?php if($level === 1): ?>
                        <div class="absolute -left-[2px] top-5 w-4 md:w-6 border-b-2 border-gray-100"></div>
                    <?php endif; ?>

                    <div class="flex gap-2 items-start">
                        
                        <?php 
                            $cmtAvatar = !empty($comment['author_avatar']) 
                                ? $comment['author_avatar'] 
                                : ($comment['user_id'] == $_SESSION['user_id'] && !empty($_SESSION['user_avatar']) 
                                    ? $_SESSION['user_avatar'] 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($comment['author_name']) . '&background=random');
                        ?>
                        <img src="<?= htmlspecialchars($cmtAvatar) ?>" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0 z-10 mt-1">
                        
                        <div class="flex-1 min-w-0">
                            <div class="bg-gray-50 rounded-2xl px-4 py-2.5 inline-block max-w-full">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-sm text-gray-900"><?= htmlspecialchars($comment['author_name']) ?></span>
                                    <?php if($comment['author_role'] == 'admin'): ?>
                                        <i class="fa-solid fa-circle-check text-blue-500 text-xs" title="Admin"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="text-gray-700 text-sm break-words whitespace-pre-wrap"><?php 
                                    if($level > 0 && !empty($comment['parent_author_name'])) {
                                        echo '<span class="font-bold text-primary mr-1">@' . htmlspecialchars($comment['parent_author_name']) . '</span>';
                                    }
                                    echo htmlspecialchars($comment['content']); 
                                ?></p>
                            </div>
                            
                            <div class="flex items-center gap-4 mt-1 ml-2 text-xs font-medium text-gray-500">
                                <span><?= date('H:i d/m/Y', strtotime($comment['created_at'])) ?></span>
                                <button @click="replyToId = <?= $comment['id'] ?>" class="hover:text-primary transition cursor-pointer font-bold">Trả lời</button>
                            </div>

                            <div x-show="replyToId === <?= $comment['id'] ?>" x-cloak class="mt-3 flex gap-3 items-start" 
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">                                 
                                <img src="<?= htmlspecialchars($currentUserAvatar) ?>" class="w-6 h-6 rounded-full object-cover border border-gray-200 shrink-0 mt-1">                                
                                <form action="?action=forum_store_comment" method="POST" class="flex-1 min-w-0">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                                    <textarea name="content" required rows="1" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary transition text-sm resize-none" placeholder="Trả lời <?= htmlspecialchars($comment['author_name']) ?>..."></textarea>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button type="button" @click="replyToId = null" class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-100 rounded-lg">Hủy</button>
                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-primary hover:bg-yellow-600 rounded-lg shadow-sm">Gửi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <?php $renderComments($comment['id'], $level + 1); ?>
                </div>
            <?php 
                endforeach; 
            }; 
            
            if (empty($commentTree[0])) {
                echo '<p class="text-center text-gray-400 italic py-4">Chưa có bình luận nào. Hãy là người mở đầu!</p>';
            } else {
                $renderComments(0, 0); 
            }
            ?>
        </div>
    </div>
</div>