<?php
// File: views/admin/system_logs.php

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ?action=home');
    exit();
}

$search = $_GET['search'] ?? '';
$actionFilter = $_GET['action_filter'] ?? 'all';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhật ký hệ thống - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f59e0b',
                        dark: '#111827',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased selection:bg-primary selection:text-white" 
      x-data="{ sidebarOpen: true, mobileSidebarOpen: false, selectedLog: null }">

<div class="min-h-screen flex">

    <?php require_once 'layouts/sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0">

        <?php require_once 'layouts/topbar.php'; ?>

        <!-- Content Area -->
        <div class="flex-1 p-4 sm:p-6 md:p-8 overflow-y-auto">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Nhật ký hệ thống</h1>
                    <p class="text-gray-500">Lưu vết và theo dõi mọi hoạt động quan trọng trong hệ thống.</p>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <form action="" method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 mb-6">
                <input type="hidden" name="action" value="admin_system_logs">
                
                <div class="flex-1 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm người dùng hoặc nội dung..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm transition-all bg-gray-50 focus:bg-white">
                </div>
                
                <div class="flex gap-2 items-center bg-gray-50 px-3 rounded-xl border border-gray-200">
                    <span class="text-sm text-gray-500 font-medium whitespace-nowrap">Từ:</span>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>" class="w-full sm:w-32 py-2.5 bg-transparent focus:outline-none text-sm text-gray-700" title="Từ ngày">
                </div>
                <div class="flex gap-2 items-center bg-gray-50 px-3 rounded-xl border border-gray-200">
                    <span class="text-sm text-gray-500 font-medium whitespace-nowrap">Đến:</span>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>" class="w-full sm:w-32 py-2.5 bg-transparent focus:outline-none text-sm text-gray-700" title="Đến ngày">
                </div>
                
                <div class="w-full sm:w-48">
                    <select name="action_filter" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm transition-all bg-gray-50 focus:bg-white appearance-none cursor-pointer">
                        <option value="all" <?= $actionFilter === 'all' ? 'selected' : '' ?>>Tất cả hành động</option>
                        <option value="tạo" <?= $actionFilter === 'tạo' ? 'selected' : '' ?>>Tạo mới</option>
                        <option value="sửa" <?= $actionFilter === 'sửa' ? 'selected' : '' ?>>Cập nhật / Sửa</option>
                        <option value="xóa" <?= $actionFilter === 'xóa' ? 'selected' : '' ?>>Xóa bỏ</option>
                        <option value="ghi danh" <?= $actionFilter === 'ghi danh' ? 'selected' : '' ?>>Ghi danh khóa học</option>
                        <option value="thanh toán" <?= $actionFilter === 'thanh toán' ? 'selected' : '' ?>>Thanh toán</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-gray-800 text-white font-medium rounded-xl hover:bg-gray-700 transition shadow-sm text-sm whitespace-nowrap">
                        Lọc dữ liệu
                    </button>
                    <a href="?action=admin_system_logs" class="flex-none px-4 py-2.5 bg-gray-100 text-gray-600 font-medium rounded-xl hover:bg-gray-200 transition text-sm whitespace-nowrap text-center" title="Xóa tất cả bộ lọc">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </form>

            <!-- Table Container -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-100">
                                <th class="py-4 px-6 font-semibold w-64">Người thao tác</th>
                                <th class="py-4 px-6 font-semibold">Nội dung hoạt động</th>
                                <th class="py-4 px-6 font-semibold w-48">Thời gian</th>
                                <th class="py-4 px-6 font-semibold w-24 text-center">Tùy chọn</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-500">
                                        <i class="fa-solid fa-ghost text-4xl mb-3 block opacity-20"></i>
                                        Không tìm thấy nhật ký nào phù hợp.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                        
                                        <!-- Cột Người Thao Tác -->
                                        <td class="py-4 px-6">
                                            <?php if ($log['user_id']): ?>
                                                <div class="flex items-center gap-3">
                                                    <?php 
                                                        $avatar = $log['avatar'] ?? '';
                                                        $avatarDisplay = !empty($avatar) 
                                                            ? (str_starts_with($avatar, 'http') ? $avatar : '/e-learning-project/public/' . $avatar) 
                                                            : 'https://ui-avatars.com/api/?name=' . urlencode($log['fullname'] ?? 'User') . '&background=random';
                                                    ?>
                                                    <img src="<?= htmlspecialchars($avatarDisplay) ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover shadow-sm border border-gray-200 bg-white shrink-0">
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-gray-800"><?= htmlspecialchars($log['fullname']) ?></span>
                                                        <span class="text-xs text-gray-400">ID: <?= $log['user_id'] ?></span>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="flex items-center gap-3 opacity-60">
                                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                                                        <i class="fa-solid fa-robot text-gray-500"></i>
                                                    </div>
                                                    <span class="font-bold text-gray-800">Hệ thống</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Cột Nội Dung Hoạt Động -->
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col items-start gap-2 max-w-xl">
                                                <?php
                                                    $actionStr = mb_strtolower($log['action']);
                                                    $actionClass = 'bg-gray-100 text-gray-600 border-gray-200/50';
                                                    if (strpos($actionStr, 'tạo') !== false || strpos($actionStr, 'ghi danh') !== false || strpos($actionStr, 'thanh toán') !== false || strpos($actionStr, 'mua') !== false) {
                                                        $actionClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200/50 shadow-sm';
                                                    } elseif (strpos($actionStr, 'xóa') !== false) {
                                                        $actionClass = 'bg-rose-50 text-rose-700 border border-rose-200/50 shadow-sm';
                                                    } elseif (strpos($actionStr, 'cập nhật') !== false || strpos($actionStr, 'sửa') !== false) {
                                                        $actionClass = 'bg-blue-50 text-blue-700 border border-blue-200/50 shadow-sm';
                                                    }
                                                ?>
                                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider <?= $actionClass ?>">
                                                    <?= htmlspecialchars($log['action']) ?>
                                                </span>
                                                <p class="text-gray-600 line-clamp-2 leading-relaxed">
                                                    <?= htmlspecialchars($log['description']) ?>
                                                </p>
                                            </div>
                                        </td>
                                        
                                        <!-- Cột Thời Gian -->
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col">
                                                <span class="text-gray-800 font-medium"><?= date('H:i', strtotime($log['created_at'])) ?></span>
                                                <span class="text-gray-400 text-xs"><?= date('d/m/Y', strtotime($log['created_at'])) ?></span>
                                            </div>
                                        </td>
                                        
                                        <!-- Cột Nút Bấm Chi Tiết -->
                                        <td class="py-4 px-6 text-center">
                                            <button @click="selectedLog = { 
                                                    id: '<?= $log['id'] ?>',
                                                    user: '<?= htmlspecialchars(addslashes($log['fullname'] ?? 'Hệ thống')) ?>',
                                                    avatar: '<?= htmlspecialchars(addslashes($avatarDisplay ?? '')) ?>',
                                                    action: '<?= htmlspecialchars(addslashes($log['action'])) ?>',
                                                    time: '<?= date('H:i d/m/Y', strtotime($log['created_at'])) ?>',
                                                    ip: '<?= htmlspecialchars(addslashes($log['ip_address'] ?? 'N/A')) ?>',
                                                    desc: '<?= htmlspecialchars(addslashes($log['description'])) ?>',
                                                    actionClass: '<?= $actionClass ?>'
                                                }" 
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-primary hover:text-white transition-colors cursor-pointer"
                                                title="Xem chi tiết">
                                                <i class="fa-solid fa-expand text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <span class="text-sm text-gray-500 font-medium">
                        Trang <?= $page ?> trên <?= $totalPages ?>
                    </span>
                    
                    <div class="flex gap-2">
                        <?php 
                            $queryStr = http_build_query(['action' => 'admin_system_logs', 'search' => $search, 'action_filter' => $actionFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo]);
                        ?>
                        <?php if ($page > 1): ?>
                            <a href="?<?= $queryStr ?>&page=<?= $page - 1 ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-300 transition text-sm font-semibold shadow-sm">Trang trước</a>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= $queryStr ?>&page=<?= $page + 1 ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 hover:border-gray-300 transition text-sm font-semibold shadow-sm">Trang sau</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
            
        </div>
        <!-- End Content Area -->

    </main>
    
    <!-- Chi Tiết Log Modal -->
    <div x-show="selectedLog" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" 
         x-cloak>
        <div x-show="selectedLog" 
             x-transition.opacity.duration.300ms
             @click="selectedLog = null"
             class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
             
        <div x-show="selectedLog" 
             x-transition.scale.95.duration.300ms
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
             
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary"></i> Chi tiết nhật ký #<span x-text="selectedLog.id"></span>
                </h3>
                <button @click="selectedLog = null" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-200 hover:text-gray-700 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 flex flex-col gap-5">
                
                <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <img x-show="selectedLog.avatar" :src="selectedLog.avatar" class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200 bg-white">
                    <div x-show="!selectedLog.avatar" class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fa-solid fa-robot text-gray-500"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-lg" x-text="selectedLog.user"></p>
                        <p class="text-xs text-gray-500 font-medium" x-text="selectedLog.time"></p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Hành động</h4>
                    <span class="px-3 py-1.5 rounded-md text-[13px] font-bold uppercase tracking-wider inline-block" 
                          :class="selectedLog.actionClass" 
                          x-text="selectedLog.action"></span>
                </div>
                
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Mô tả chi tiết</h4>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-gray-700 text-sm leading-relaxed whitespace-pre-wrap" x-text="selectedLog.desc"></div>
                </div>
                
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Bảo mật (IP Address)</h4>
                    <div class="flex items-center gap-2 text-gray-600 font-mono text-sm bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <i class="fa-solid fa-network-wired text-gray-400"></i>
                        <span x-text="selectedLog.ip"></span>
                    </div>
                </div>
                
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button @click="selectedLog = null" class="px-5 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-100 transition shadow-sm">
                    Đóng lại
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
</body>
</html>
