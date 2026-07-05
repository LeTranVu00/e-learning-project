<?php
$pageTitle = 'Hồ sơ cá nhân';
require_once 'layouts/header.php';

// $user là biến được truyền từ ProfileController
$avatar = !empty($user['avatar']) ? $user['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['fullname']) . '&background=random';
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700" data-aos="fade-up">
        
        <!-- Header Banner -->
        <div class="h-32 bg-gradient-to-r from-primary to-yellow-500 relative">
            <div class="absolute -bottom-16 left-8">
                <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" referrerpolicy="no-referrer" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 object-cover shadow-lg bg-white">
            </div>
            <div class="absolute top-4 right-4">
                <span class="bg-white/20 backdrop-blur-md text-white px-4 py-1.5 rounded-full text-sm font-semibold border border-white/30 shadow-sm">
                    Vai trò: <?= $user['role'] === 'admin' ? 'Quản trị viên' : 'Học viên' ?>
                </span>
            </div>
        </div>

        <div class="pt-20 px-8 pb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($user['fullname']) ?></h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1"><i class="fa-solid fa-envelope mr-1"></i> <?= htmlspecialchars($user['email']) ?></p>
            <p class="text-sm text-gray-400 mt-1">Tham gia từ: <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            
            <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-8">
                <form action="?action=update_profile" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Họ và tên -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="fullname" required value="<?= htmlspecialchars($user['fullname']) ?>" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition shadow-sm">
                        </div>
                        
                        <!-- Số điện thoại -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Số điện thoại</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition shadow-sm" placeholder="VD: 0912345678">
                        </div>
                        
                        <!-- Địa chỉ -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Địa chỉ</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition shadow-sm" placeholder="Nhập địa chỉ của bạn...">
                        </div>
                        
                        <!-- Tiểu sử -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Giới thiệu bản thân (Bio)</label>
                            <textarea name="bio" rows="4" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary transition resize-none shadow-sm" placeholder="Hãy viết một vài điều về bản thân bạn..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
