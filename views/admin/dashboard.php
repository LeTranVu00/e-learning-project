<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans flex min-h-screen" x-data="{ sidebarOpen: true }">

    <aside class="w-64 bg-dark text-white transition-all duration-300 flex flex-col shadow-2xl relative z-20" :class="sidebarOpen ? '' : '!w-20'">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-lg tracking-wider transition-opacity duration-300">ADMIN PANEL</span>
        </div>

        <nav class="flex-1 px-2 py-6 space-y-2">
            <a href="?action=admin_dashboard" class="flex items-center px-4 py-3 bg-gray-800 text-primary rounded-xl transition group">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Tổng quan</span>
            </a>
            
            <a href="?action=admin_manage_courses" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-book-open w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Khóa học</span>        
            </a>

            <a href="#" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-users w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Học viên</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <a href="?action=home" class="flex items-center px-4 py-3 text-gray-400 hover:bg-red-500 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Thoát Admin</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '') ?>" class="w-9 h-9 rounded-full border-2 border-primary object-cover">
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Bảng điều khiển</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-primary">
                    <div class="w-14 h-14 bg-yellow-50 rounded-full flex items-center justify-center text-primary text-2xl">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tổng Khóa Học</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_courses ?? 0 ?></p>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-blue-500">
                    <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-2xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tổng Học Viên</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($total_users ?? 0) ?></p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-green-500">
                    <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center text-green-500 text-2xl">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Lượt Ghi Danh</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($total_enrollments ?? 0) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center mt-8">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/data-analysis-4900769-4081076.png" alt="Analytics" class="w-64 mb-4 opacity-80">
                <h2 class="text-xl font-bold text-gray-800">Tính năng Phân tích dữ liệu</h2>
                <p class="text-gray-500 mt-2">Biểu đồ doanh thu và tiến độ học viên sẽ sớm được cập nhật tại đây.</p>
            </div>

        </div>
    </main>
</body>
</html>