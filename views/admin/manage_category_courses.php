<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết Danh mục: <?= htmlspecialchars($category['name']) ?> - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script> tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } } </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-dark text-white transition-all duration-300 flex flex-col shadow-2xl relative z-20" :class="sidebarOpen ? '' : '!w-20'">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-lg tracking-wider transition-opacity duration-300">ADMIN PANEL</span>
        </div>

        <nav class="flex-1 px-2 py-6 space-y-2 overflow-y-auto">
            <a href="?action=admin_dashboard" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Tổng quan</span>
            </a>
            
            <a href="?action=admin_manage_courses" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-book-open w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Khóa học</span>
            </a>
            
            <a href="?action=admin_manage_categories" class="flex items-center px-4 py-3 bg-gray-800 text-primary rounded-xl transition group">
                <i class="fa-solid fa-folder-tree w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Danh mục</span>
            </a>

            <a href="?action=admin_manage_comments" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-comments w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Bình luận</span>
            </a>

            <a href="?action=admin_manage_users" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-users w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Người dùng</span>
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

        <div class="flex-1 overflow-y-auto p-6 md:p-8 bg-gray-50/50">
            
            <!-- Toast Notification -->
            <?php if (isset($_SESSION['success'])): ?>
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full"
                     class="fixed top-20 right-6 z-50 flex items-center bg-white border-l-4 border-green-500 rounded-xl shadow-xl p-4 min-w-[300px]">
                    <div class="text-green-500 mr-3">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Thành công!</h4>
                        <p class="text-sm text-gray-600"><?= $_SESSION['success'] ?></p>
                    </div>
                    <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="?action=admin_manage_categories" class="text-gray-400 hover:text-primary transition bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Danh mục: <?= htmlspecialchars($category['name']) ?></h1>
                    </div>
                    <p class="text-gray-500 mt-1">Quản lý các khóa học đang thuộc danh mục này.</p>
                </div>
                <button onclick="openAddCourseModal()" class="bg-primary hover:bg-yellow-600 text-white px-6 py-2.5 rounded-xl font-medium transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-link"></i> Thêm khóa học vào danh mục
                </button>
            </div>

            <!-- Bảng Khóa học trong Danh mục -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-sm text-gray-500 uppercase">
                                <th class="py-4 px-6 font-semibold">Hình ảnh</th>
                                <th class="py-4 px-6 font-semibold">Tên Khóa học</th>
                                <th class="py-4 px-6 font-semibold">Giảng viên</th>
                                <th class="py-4 px-6 font-semibold text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (!empty($category_courses)): ?>
                                <?php foreach ($category_courses as $course): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150 group">
                                        <td class="py-4 px-6 w-32">
                                            <img src="<?= htmlspecialchars($course['thumbnail']) ?>" class="w-16 h-12 object-cover rounded-lg border border-gray-200" alt="thumbnail">
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-gray-900"><?= htmlspecialchars($course['title']) ?></div>
                                            <div class="text-sm text-gray-500 mt-1"><?= number_format($course['price']) ?> VNĐ</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-gray-700"><?= htmlspecialchars($course['instructor']) ?></span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button onclick="confirmRemoveCourse(<?= $course['id'] ?>, <?= $category['id'] ?>)" class="text-red-600 bg-red-50 hover:bg-red-500 hover:text-white px-3 py-2 rounded-lg transition shadow-sm border border-red-100 flex items-center gap-1" title="Gỡ khóa học">
                                                    <i class="fa-solid fa-link-slash"></i> <span class="text-sm font-medium">Gỡ</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-gray-500">Chưa có khóa học nào trong danh mục này.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Gắn Khóa học vào Danh mục -->
            <div id="addCourseModal" class="fixed inset-0 z-[100] hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
                
                <!-- Modal Content -->
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-gray-100">
                        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                            <h3 class="text-xl font-bold flex items-center gap-2"><i class="fa-solid fa-link text-primary"></i> Gắn Khóa học</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="bg-white px-6 py-6">
                            <form action="?action=admin_update_course_category" method="POST">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Chọn khóa học</label>
                                        <select name="course_id" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:bg-white outline-none transition shadow-sm cursor-pointer appearance-none">
                                            <option value="">-- Chọn một khóa học --</option>
                                            <?php if (!empty($available_courses)): ?>
                                                <?php foreach ($available_courses as $c): ?>
                                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php if(empty($available_courses)): ?>
                                            <p class="text-red-500 text-sm mt-2"><i class="fa-solid fa-circle-info"></i> Không có khóa học nào trống để thêm.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition">
                                        Hủy
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 bg-dark text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-sm flex items-center gap-2" <?= empty($available_courses) ? 'disabled' : '' ?>>
                                        <i class="fa-solid fa-check"></i> Xác nhận
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const modal = document.getElementById('addCourseModal');

                function openAddCourseModal() {
                    modal.classList.remove('hidden');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                }

                function confirmRemoveCourse(courseId, categoryId) {
                    if(confirm('Bạn có chắc chắn muốn gỡ khóa học này khỏi danh mục không?')) {
                        window.location.href = "?action=admin_remove_course_category&course_id=" + courseId + "&category_id=" + categoryId;
                    }
                }
            </script>

        </div>
    </main>
</body>
</html>
