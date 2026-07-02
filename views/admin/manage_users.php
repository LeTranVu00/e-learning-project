<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script> tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } } </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden" 
      x-data="{ 
          sidebarOpen: true, 
          showEditModal: false,
          showDetailModal: false,
          editData: {},
          detailData: {},
          openEdit(user) {
              this.editData = { ...user };
              this.showEditModal = true;
          }
      }">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-dark text-white transition-all duration-300 flex flex-col shadow-2xl relative z-20" :class="sidebarOpen ? '' : '!w-20'">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-lg tracking-wider">ADMIN PANEL</span>
        </div>
        <nav class="flex-1 px-2 py-6 space-y-2">
            <a href="?action=admin_dashboard" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Tổng quan</span>
            </a>
            <a href="?action=admin_manage_courses" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-book-open w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Khóa học</span>
            </a>
            <a href="?action=admin_manage_comments" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-comments w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Bình luận</span>
            </a>
            <a href="?action=admin_manage_users" class="flex items-center px-4 py-3 bg-gray-800 text-primary rounded-xl transition group">
                <i class="fa-solid fa-users w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Người dùng</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <a href="?action=home" class="flex items-center px-4 py-3 text-gray-400 hover:bg-red-500 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Thoát Admin</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary focus:outline-none"><i class="fa-solid fa-bars text-xl"></i></button>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '') ?>" class="w-9 h-9 rounded-full border-2 border-primary object-cover">
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Quản lý Người dùng</h1>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-10 opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-10 opacity-0"
                     class="fixed top-20 right-6 z-50 bg-white border-l-4 border-green-500 shadow-xl rounded-xl px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Thành công!</h4>
                        <p class="text-sm text-gray-600"><?= $_SESSION['success']; ?></p>
                    </div>
                    <button @click="show = false" class="ml-2 text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-10 opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-10 opacity-0"
                     class="fixed top-20 right-6 z-50 bg-white border-l-4 border-red-500 shadow-xl rounded-xl px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-exclamation text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Lỗi!</h4>
                        <p class="text-sm text-gray-600"><?= $_SESSION['error']; ?></p>
                    </div>
                    <button @click="show = false" class="ml-2 text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- BỘ LỌC -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
                <form method="GET" action="index.php" class="flex flex-wrap gap-4 items-end">
                    <input type="hidden" name="action" value="admin_manage_users">
                    
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tìm kiếm</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Tên người dùng, Email..." class="w-full h-[40px] px-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Vai trò</label>
                        <select name="role" class="h-[40px] px-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm font-medium text-gray-700">
                            <option value="all" <?= ($_GET['role'] ?? 'all') === 'all' ? 'selected' : '' ?>>Tất cả</option>
                            <option value="student" <?= ($_GET['role'] ?? '') === 'student' ? 'selected' : '' ?>>Học viên</option>
                            <option value="instructor" <?= ($_GET['role'] ?? '') === 'instructor' ? 'selected' : '' ?>>Giảng viên</option>
                            <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sắp xếp</label>
                        <select name="sort" class="h-[40px] px-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm font-medium text-gray-700">
                            <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="px-5 h-[40px] flex items-center justify-center bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-xl transition text-sm">
                        <i class="fa-solid fa-filter mr-2"></i> Lọc
                    </button>
                    
                    <?php if(!empty($_GET['search']) || (isset($_GET['role']) && $_GET['role'] !== 'all') || (!empty($_GET['sort']) && $_GET['sort'] !== 'latest')): ?>
                    <a href="?action=admin_manage_users" class="px-5 h-[40px] flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition text-sm">
                        Xóa lọc
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- BẢNG DỮ LIỆU -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider">
                            <th class="p-4 font-semibold">Người dùng</th>
                            <th class="p-4 font-semibold">Vai trò</th>
                            <th class="p-4 font-semibold text-center">Tham gia lúc</th>
                            <th class="p-4 font-semibold text-center w-48">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(!empty($users)): foreach($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <?php 
                                        $avatar = $user['avatar'] ?? '';
                                        $avatarDisplay = !empty($avatar) 
                                            ? (str_starts_with($avatar, 'http') ? $avatar : '/e-learning-project/public/' . $avatar) 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($user['fullname']) . '&background=random';
                                    ?>
                                    <img src="<?= htmlspecialchars($avatarDisplay) ?>" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800"><?= htmlspecialchars($user['fullname']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <?php if($user['role'] === 'admin'): ?>
                                    <span class="px-3 py-1 bg-red-50 text-red-600 border border-red-200 rounded-full text-xs font-bold uppercase tracking-wider">Admin</span>
                                <?php elseif($user['role'] === 'instructor'): ?>
                                    <span class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-full text-xs font-bold uppercase tracking-wider">Giảng viên</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-green-50 text-green-600 border border-green-200 rounded-full text-xs font-bold uppercase tracking-wider">Học viên</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center text-sm text-gray-600">
                                <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button @click="detailData = <?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>; showDetailModal = true" title="Xem chi tiết" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition shadow-sm border border-gray-200">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    
                                    <button @click="openEdit(<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>)" title="Cập nhật tài khoản" class="w-10 h-10 rounded-full flex items-center justify-center bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition shadow-sm border border-yellow-100">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    
                                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                                    <a href="?action=admin_delete_user&id=<?= $user['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn XÓA tài khoản này không? Mọi dữ liệu liên quan có thể sẽ bị ảnh hưởng!');" title="Xóa tài khoản" class="w-10 h-10 rounded-full flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition shadow-sm border border-red-100">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                    <?php else: ?>
                                    <div class="w-10 h-10"></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="p-8 text-center text-gray-500">Chưa có người dùng nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PHÂN TRANG -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
            <?php 
                $filterParams = '';
                if (!empty($_GET['search'])) $filterParams .= '&search=' . urlencode($_GET['search']);
                if (isset($_GET['role']) && $_GET['role'] !== 'all') $filterParams .= '&role=' . urlencode($_GET['role']);
                if (!empty($_GET['sort'])) $filterParams .= '&sort=' . urlencode($_GET['sort']);
            ?>
            <div class="mt-6 flex justify-center">
                <nav class="flex items-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?action=admin_manage_users&page=<?= $page - 1 ?><?= $filterParams ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                            <i class="fa-solid fa-chevron-left text-sm"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?action=admin_manage_users&page=<?= $i ?><?= $filterParams ?>" class="px-4 py-2 <?= $i === $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' ?> border rounded-lg font-medium transition">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?action=admin_manage_users&page=<?= $page + 1 ?><?= $filterParams ?>" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL SỬA NGƯỜI DÙNG -->
    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showEditModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showEditModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-lg z-50 flex flex-col overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold flex items-center gap-2"><i class="fa-solid fa-user-pen text-primary"></i> Cập nhật Người dùng</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=admin_update_user" method="POST" id="formEdit" class="space-y-5">
                        <input type="hidden" name="id" x-model="editData.id">
                        
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Họ và Tên</label>
                            <input type="text" name="fullname" x-model="editData.fullname" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Email</label>
                            <input type="text" x-model="editData.email" readonly class="w-full px-4 py-3 border rounded-xl outline-none bg-gray-100 text-gray-500 cursor-not-allowed">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Vai trò (Role)</label>
                            <select name="role" x-model="editData.role" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                                <option value="student">Học viên (Student)</option>
                                <option value="instructor">Giảng viên (Instructor)</option>
                                <option value="admin">Quản trị viên (Admin)</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                    <button @click="showEditModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 transition font-medium">Hủy</button>
                    <button type="submit" form="formEdit" class="bg-primary text-white font-bold py-2.5 px-6 rounded-xl hover:bg-yellow-600 transition">
                        <i class="fa-solid fa-floppy-disk mr-2"></i>Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL XEM CHI TIẾT -->
    <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showDetailModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div x-show="showDetailModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-sm z-50 flex flex-col overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Thông tin tài khoản</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow space-y-4">
                    <div class="flex flex-col items-center gap-3">
                        <img :src="detailData.avatar && detailData.avatar.startsWith('http') ? detailData.avatar : (detailData.avatar ? '/e-learning-project/public/' + detailData.avatar : 'https://ui-avatars.com/api/?name=' + encodeURIComponent(detailData.fullname || '') + '&background=random')" class="w-24 h-24 object-cover rounded-full border-4 border-white shadow-lg">
                        <div class="text-center">
                            <h4 class="text-xl font-bold text-gray-800 leading-tight" x-text="detailData.fullname"></h4>
                            <p class="text-gray-500 text-sm mt-1" x-text="detailData.email"></p>
                        </div>
                        <div class="mt-2">
                            <template x-if="detailData.role === 'admin'">
                                <span class="px-3 py-1 bg-red-50 text-red-600 border border-red-200 rounded-full text-xs font-bold uppercase tracking-wider">Admin</span>
                            </template>
                            <template x-if="detailData.role === 'instructor'">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-full text-xs font-bold uppercase tracking-wider">Giảng viên</span>
                            </template>
                            <template x-if="detailData.role === 'student'">
                                <span class="px-3 py-1 bg-green-50 text-green-600 border border-green-200 rounded-full text-xs font-bold uppercase tracking-wider">Học viên</span>
                            </template>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-4 border mt-4 space-y-3">
                        <div class="flex flex-col border-b pb-2">
                            <span class="text-gray-500 text-xs font-medium uppercase mb-1">Thời gian tạo tài khoản</span>
                            <span class="font-semibold text-gray-800" x-text="detailData.created_at ? new Date(detailData.created_at.replace(' ', 'T')).toLocaleString('vi-VN') : '—'"></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-500 text-xs font-medium uppercase mb-1">Phương thức đăng nhập</span>
                            <span class="font-semibold text-gray-800" x-text="detailData.google_id ? 'Google (SSO)' : 'Email & Mật khẩu'"></span>
                        </div>
                    </div>

                    <!-- KHÓA HỌC ĐÃ ĐĂNG KÝ -->
                    <div class="mt-4 border-t pt-4">
                        <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-3"><i class="fa-solid fa-book-open text-primary mr-1"></i> Khóa học đã đăng ký</h4>
                        <template x-if="!detailData.enrolled_courses || detailData.enrolled_courses.length === 0">
                            <p class="text-gray-500 text-sm italic text-center py-4 bg-gray-50 rounded-xl border border-dashed">Chưa đăng ký khóa học nào.</p>
                        </template>
                        <template x-if="detailData.enrolled_courses && detailData.enrolled_courses.length > 0">
                            <ul class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="course in detailData.enrolled_courses" :key="course.id">
                                    <li class="bg-gray-50 border rounded-xl p-3 flex gap-3 items-center hover:bg-white hover:shadow-sm transition">
                                        <img :src="course.thumbnail && course.thumbnail.startsWith('http') ? course.thumbnail : (course.thumbnail ? '/e-learning-project/public/' + course.thumbnail : 'https://placehold.co/100x100?text=No+Image')" class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-800 truncate" x-text="course.title"></p>
                                            <p class="text-xs text-gray-500 mt-0.5">Ngày ĐK: <span class="font-medium text-gray-700" x-text="course.enrolled_at ? new Date(course.enrolled_at.replace(' ', 'T')).toLocaleDateString('vi-VN') : '—'"></span></p>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end shrink-0">
                    <button @click="showDetailModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 transition font-medium">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
