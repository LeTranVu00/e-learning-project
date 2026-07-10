<?php $pageTitle = 'Bảng điều khiển';
require_once 'layouts/header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4" data-aos="fade-up">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Bảng điều khiển</h1>
        <p class="text-sm text-gray-500 mt-1">Tổng quan hệ thống E-Learning</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-primary transition-all duration-300 hover:shadow-md hover:-translate-y-1"
        data-aos="fade-up" data-aos-delay="50">
        <div
            class="w-12 h-12 sm:w-14 sm:h-14 bg-yellow-50 rounded-xl flex items-center justify-center text-primary text-xl sm:text-2xl shrink-0">
            <i class="fa-solid fa-book"></i>
        </div>
        <div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">Tổng Khóa Học</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                <?= number_format($total_courses ?? 0) ?></p>
        </div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-blue-500 transition-all duration-300 hover:shadow-md hover:-translate-y-1"
        data-aos="fade-up" data-aos-delay="100">
        <div
            class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 text-xl sm:text-2xl shrink-0">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">Tổng Học Viên</p>
            <div class="flex items-end gap-2">
                <p class="text-xl sm:text-2xl font-bold text-gray-800">
                    <?= number_format($total_users ?? 0) ?></p>
                <?php if (!empty($today_users) && $today_users > 0): ?>
                    <span
                        class="text-xs font-bold text-green-500 bg-green-50 px-1.5 py-0.5 rounded mb-1">+<?= $today_users ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-green-500 transition-all duration-300 hover:shadow-md hover:-translate-y-1"
        data-aos="fade-up" data-aos-delay="150">
        <div
            class="w-12 h-12 sm:w-14 sm:h-14 bg-green-50 rounded-xl flex items-center justify-center text-green-500 text-xl sm:text-2xl shrink-0">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">Lượt Ghi Danh</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                <?= number_format($total_enrollments ?? 0) ?></p>
        </div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-emerald-600 transition-all duration-300 hover:shadow-md hover:-translate-y-1"
        data-aos="fade-up" data-aos-delay="200">
        <div
            class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 text-xl sm:text-2xl shrink-0">
            <i class="fa-solid fa-money-bill-trend-up"></i>
        </div>
        <div>
            <p class="text-xs sm:text-sm text-gray-500 font-medium">Tổng Doanh Thu</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">
                <?= number_format($total_revenue ?? 0, 0, ',', '.') ?>đ</p>
            <?php if (!empty($today_revenue) && $today_revenue > 0): ?>
                <p class="text-xs text-green-500 font-medium mt-0.5"><i
                        class="fa-solid fa-arrow-trend-up mr-1"></i>+<?= number_format($today_revenue, 0, ',', '.') ?>đ</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 transition-colors"
        data-aos="fade-up" data-aos-delay="250">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Doanh thu & Đăng ký năm nay</h2>
        <div id="revenueChart"></div>
    </div>

    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 transition-colors"
        data-aos="fade-up" data-aos-delay="300">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Top Khóa học nổi bật</h2>
        <div id="courseDonutChart" class="flex justify-center"></div>
    </div>
</div>

<!-- Tables & Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-start">
    <!-- Transactions Table -->
    <div class="lg:col-span-2 bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 transition-colors overflow-hidden"
        data-aos="fade-up" data-aos-delay="350">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-800">Giao dịch mới nhất</h2>
            <a href="?action=admin_manage_users"
                class="text-sm font-medium text-primary hover:text-yellow-600 transition">Xem tất cả →</a>
        </div>

        <div class="overflow-x-auto -mx-5 sm:mx-0">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-gray-100">
                        <th class="pb-3 font-medium pl-5 sm:pl-0">Học viên</th>
                        <th class="pb-3 font-medium">Khóa học</th>
                        <th class="pb-3 font-medium text-right">Số tiền</th>
                        <th class="pb-3 font-medium text-right pr-5 sm:pr-0">Ngày</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latest_transactions)): ?>
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-2xl mb-2 block opacity-50"></i>
                                Chưa có giao dịch nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($latest_transactions as $tx): ?>
                            <tr
                                class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="py-4 pl-5 sm:pl-0">
                                    <div class="flex items-center gap-3">
                                        <img src="<?= htmlspecialchars($tx['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($tx['fullname']) . '&background=random') ?>"
                                            class="w-8 h-8 rounded-lg object-cover shadow-sm">
                                        <span
                                            class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($tx['fullname']) ?></span>
                                    </div>
                                </td>
                                <td class="py-4 text-gray-600 text-sm max-w-[150px] truncate">
                                    <?= htmlspecialchars($tx['title']) ?></td>
                                <td class="py-4 text-right">
                                    <span
                                        class="inline-block px-2.5 py-1 bg-green-50 text-green-600 font-bold rounded-lg text-xs">
                                        <?= number_format($tx['price'], 0, ',', '.') ?>đ
                                    </span>
                                </td>
                                <td class="py-4 text-right text-gray-400 text-xs pr-5 sm:pr-0">
                                    <?= date('d/m/Y', strtotime($tx['enrolled_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 transition-colors"
        data-aos="fade-up" data-aos-delay="400">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Hoạt động gần đây</h2>

        <div class="overflow-y-auto max-h-[420px] pr-2 -mr-2">
            <div
                class="space-y-5 relative before:absolute before:inset-0 before:ml-[15px] before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                <?php if (empty($recent_activities)): ?>
                <p class="text-gray-500 text-sm text-center py-4">
                    <i class="fa-solid fa-clock text-2xl mb-2 block opacity-50"></i>
                    Chưa có hoạt động nào
                </p>
            <?php else: ?>
                <?php foreach ($recent_activities as $act): ?>
                    <div class="relative flex items-start gap-3">
                        <?php
                        $iconBg = 'bg-gray-100 text-gray-500';
                        $iconClass = 'fa-solid fa-bell';
                        $actionText = 'đã thực hiện hành động';
                        $extraTextClass = 'text-primary font-medium';
                        if ($act['type'] === 'user') {
                            $iconBg = 'bg-blue-100 text-blue-500';
                            $iconClass = 'fa-solid fa-user-plus';
                            $actionText = 'đã đăng ký tài khoản';
                        } elseif ($act['type'] === 'enrollment') {
                            $iconBg = 'bg-green-100 text-green-500';
                            $iconClass = 'fa-solid fa-cart-shopping';
                            $actionText = 'đã mua khóa học';
                            $extraText = $act['extra_info'] ?? '';
                        } elseif ($act['type'] === 'comment') {
                            $iconBg = 'bg-yellow-100 text-yellow-500';
                            $iconClass = 'fa-regular fa-comment';
                            $actionText = 'đã bình luận';
                            $extraText = mb_strimwidth($act['extra_info'] ?? '', 0, 40, '...');
                            $extraTextClass = 'text-gray-500 italic border-l-2 border-gray-200 pl-2';
                        }
                        ?>
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 z-10 <?= $iconBg ?> shadow-sm border-2 border-white">
                            <i class="<?= $iconClass ?> text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">
                                <span class="font-bold"><?= htmlspecialchars($act['fullname']) ?></span>
                                <span class="text-gray-500"><?= $actionText ?></span>
                            </p>
                            <?php if ($extraText): ?>
                                <p class="text-xs <?= $extraTextClass ?> truncate mt-1.5"><?= htmlspecialchars($extraText) ?></p>
                            <?php endif; ?>
                            <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1">
                                <i class="fa-regular fa-clock"></i>
                                <?php
                                $diff = time() - strtotime($act['created_at']);
                                if ($diff < 60)
                                    echo 'Vừa xong';
                                elseif ($diff < 3600)
                                    echo floor($diff / 60) . ' phút trước';
                                elseif ($diff < 86400)
                                    echo floor($diff / 3600) . ' giờ trước';
                                elseif ($diff < 604800)
                                    echo floor($diff / 86400) . ' ngày trước';
                                elseif ($diff < 2592000)
                                    echo floor($diff / 604800) . ' tuần trước';
                                else
                                    echo date('d/m/Y', strtotime($act['created_at']));
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize AOS
    AOS.init({ duration: 600, easing: 'ease-in-out', once: true });

    // Revenue Chart
    var revenueOptions = {
        series: [{
            name: 'Doanh thu (VNĐ)',
            data: <?= json_encode($chart_revenue ?? []) ?>
        }, {
            name: 'Ghi danh',
            data: <?= json_encode($chart_enrollments ?? []) ?>
        }],
        chart: {
            height: 350,
            type: 'bar',
            fontFamily: 'inherit',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        colors: ['#10b981', '#f59e0b'],
        labels: <?= json_encode($chart_months ?? []) ?>,
        yaxis: [{
            title: { text: 'Doanh thu (VNĐ)', style: { fontWeight: 500 } },
            min: 0,
            tickAmount: 5,
            decimalsInFloat: 0,
            max: function(val) { return val < 500000 ? 500000 : val; },
            labels: { formatter: function (y) { return y.toLocaleString('vi-VN') + ' đ'; } }
        }, {
            opposite: true,
            title: { text: 'Số lượt ghi danh', style: { fontWeight: 500 } },
            min: 0,
            tickAmount: 5,
            decimalsInFloat: 0,
            max: function(val) { return val < 5 ? 5 : val; },
            labels: { formatter: function (val) { return Math.round(val).toString(); } }
        }],
        grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
        theme: { mode: 'light' }
    };
    var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();

    // Donut Chart
    var donutOptions = {
        series: <?= !empty($chart_course_series) ? json_encode($chart_course_series) : '[]' ?>,
        labels: <?= !empty($chart_course_labels) ? json_encode($chart_course_labels) : '[]' ?>,
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: 'inherit',
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4445'],
        plotOptions: { pie: { donut: { size: '65%' } } },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', itemMargin: { horizontal: 10 } },
        noData: { text: 'Chưa có dữ liệu', align: 'center', verticalAlign: 'middle', style: { fontSize: '14px' } },
        responsive: [{ breakpoint: 480, options: { chart: { height: 280 }, legend: { position: 'bottom' } } }],
        theme: { mode: 'light' }
    };
    var donutChart = new ApexCharts(document.querySelector("#courseDonutChart"), donutOptions);
    donutChart.render();

</script>

<?php require_once 'layouts/footer.php'; ?>