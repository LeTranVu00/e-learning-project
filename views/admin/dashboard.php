<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

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

            <a href="?action=admin_manage_comments" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-comments w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Bình luận</span>
            </a>

            <a href="?action=admin_manage_users" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-users w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Người dùng</span>
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
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-primary">
                    <div class="w-14 h-14 bg-yellow-50 rounded-full flex items-center justify-center text-primary text-2xl">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tổng Khóa Học</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($total_courses ?? 0) ?></p>
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
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Lượt Ghi Danh</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($total_enrollments ?? 0) ?></p>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-green-600">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-2xl">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Doanh Thu</p>
                        <p class="text-2xl font-bold text-gray-800"><?= number_format($total_revenue ?? 0, 0, ',', '.') ?>đ</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Biểu đồ Doanh thu -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Doanh thu & Đăng ký năm nay</h2>
                    <div id="revenueChart"></div>
                </div>
                
                <!-- Biểu đồ Phân bổ khóa học -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Top Khóa học nổi bật</h2>
                    <div id="courseDonutChart" class="flex justify-center"></div>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Data for Revenue & Enrollments Chart
        var revenueOptions = {
            series: [{
                name: 'Doanh thu (VNĐ)',
                type: 'column',
                data: <?= json_encode($chart_revenue ?? []) ?>
            }, {
                name: 'Ghi danh',
                type: 'line',
                data: <?= json_encode($chart_enrollments ?? []) ?>
            }],
            chart: {
                height: 350,
                type: 'line',
                fontFamily: 'inherit',
                toolbar: { show: false }
            },
            stroke: {
                width: [0, 4]
            },
            colors: ['#10b981', '#f59e0b'],
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1],
                formatter: function (val) { return parseInt(val).toLocaleString('vi-VN'); }
            },
            labels: <?= json_encode($chart_months ?? []) ?>,
            yaxis: [{
                title: { text: 'Doanh thu (VNĐ)', style: { fontWeight: 500 } },
                labels: { formatter: function (y) { return y.toLocaleString('vi-VN') + " đ"; } }
            }, {
                opposite: true,
                title: { text: 'Số lượt ghi danh', style: { fontWeight: 500 } },
                decimalsInFloat: 0,
                labels: { formatter: function (val) { return parseInt(val).toLocaleString('vi-VN'); } }
            }]
        };
        var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
        revenueChart.render();

        // Data for Top Courses Donut Chart
        var donutOptions = {
            series: <?= json_encode($chart_course_series ?? []) ?>,
            labels: <?= json_encode($chart_course_labels ?? []) ?>,
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'inherit',
            },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom'
            }
        };
        var donutChart = new ApexCharts(document.querySelector("#courseDonutChart"), donutOptions);
        donutChart.render();
    </script>
</body>
</html>