<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning - Nền Tảng Học Trực Tuyến</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { primary: '#f59e0b', dark: '#111827' }
                }
            }
        }
    </script>

    <!-- Ngăn chặn nhấp nháy màn hình (FOUC) khi tải trang -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Thêm AOS Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Thêm CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            overflow-y: scroll !important;
            scroll-behavior: smooth;
        }

        body {
            animation: smoothLoad 0.2s ease-in-out;
        }

        @keyframes smoothLoad {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        .dark ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #f59e0b, #d97706); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(to bottom, #d97706, #b45309); }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Animation Keyframes (Global) */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: 0 0 40px rgba(245, 158, 11, 0.6); }
        }

        .animate-bounce-slow { animation: bounce-slow 3s ease-in-out infinite; }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #d97706, #f59e0b);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s ease-in-out infinite;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass {
            background: rgba(31, 41, 55, 0.7);
            border: 1px solid rgba(75, 85, 99, 0.3);
        }

        /* CKEditor Dark Mode */
        .dark .ck.ck-editor__main > .ck-editor__editable {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }
        .dark .ck.ck-toolbar {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
        }
        .dark .ck.ck-button {
            color: #d1d5db !important;
        }
        .dark .ck.ck-button:hover {
            background-color: #374151 !important;
        }
    </style>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body
    class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 font-sans flex flex-col min-h-screen transition-colors duration-300">

    <?php
    $cart_count = 0;
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        require_once __DIR__ . '/../../app/config/Database.php';
        require_once __DIR__ . '/../../app/models/Course.php';
        $db = (new Database())->getConnection();
        $courseModel = new Course($db);
        $valid_cart = [];
        foreach ($_SESSION['cart'] as $c_id) {
            if ($courseModel->getCourseById($c_id)) {
                $valid_cart[] = $c_id;
            }
        }
        $_SESSION['cart'] = $valid_cart;
        $cart_count = count($_SESSION['cart']);
    }
    ?>
    <!-- ==================== NAVBAR ==================== -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-dark text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="?action=home" class="flex items-center gap-2 text-xl font-bold tracking-wider shrink-0">
                    <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
                    <span>E-LEARNING</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-1 flex-1 justify-center">
                    <a href="?action=home"
                        class="text-gray-300 hover:text-primary px-4 py-2 rounded-lg text-sm font-medium transition-colors">Trang
                        chủ</a>
                    <a href="?action=courses"
                        class="text-gray-300 hover:text-primary px-4 py-2 rounded-lg text-sm font-medium transition-colors">Khóa
                        học</a>
                    <a href="?action=forum"
                        class="text-gray-300 hover:text-primary px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5">
                        Diễn đàn
                    </a>
                    <a href="?action=home#contact"
                        class="text-gray-300 hover:text-primary px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1.5">
                        Liên hệ
                    </a>
                </div>

                <!-- Auth Section & Dark Mode -->
                <div class="hidden md:flex items-center shrink-0">

                    <!-- Cart Icon Desktop -->
                    <a href="?action=cart" class="relative text-gray-400 hover:text-primary mr-2 p-2 transition-colors group" title="Giỏ hàng">
                        <i class="fa-solid fa-cart-shopping text-xl group-hover:scale-110 transition-transform"></i>
                        <span id="cart-badge-desktop" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full transform translate-x-1/4 -translate-y-1/4 <?= $cart_count > 0 ? '' : 'hidden' ?>">
                            <?= $cart_count ?>
                        </span>
                    </a>

                    <!-- Dark Mode Toggle Desktop -->
                    <button onclick="toggleDarkMode()"
                        class="text-gray-400 hover:text-yellow-400 mr-4 p-2 rounded-full hover:bg-gray-800 transition-colors"
                        title="Bật/Tắt giao diện tối">
                        <i class="fa-solid fa-moon dark:hidden text-lg"></i>
                        <i class="fa-solid fa-sun hidden dark:inline text-lg text-yellow-400"></i>
                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div x-data="{ profileOpen: false }" class="relative" 
                             @mouseenter="profileOpen = true" 
                             @mouseleave="profileOpen = false">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                                class="flex items-center gap-2.5 hover:bg-gray-800 px-3 py-2 rounded-lg transition duration-200">
                                <?php 
                                    $headerAvatar = !empty($_SESSION['user_avatar']) ? $_SESSION['user_avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_name'] ?? 'User') . '&background=random';
                                ?>
                                <img src="<?= htmlspecialchars($headerAvatar) ?>" alt="Avatar" referrerpolicy="no-referrer"
                                    class="w-8 h-8 rounded-full border border-gray-500 object-cover">
                                <span class="text-sm font-medium text-gray-200">
                                    Chào, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                                    :class="profileOpen ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="profileOpen" @click.away="profileOpen = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-cloak
                                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-lg py-2 z-50 before:content-[''] before:absolute before:-top-4 before:left-0 before:w-full before:h-4 before:bg-transparent">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wider">Vai trò</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-200">
                                        <?= $_SESSION['user_role'] === 'admin' ? 'Quản trị viên' : 'Học viên' ?>
                                    </p>
                                </div>

                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <a href="?action=admin_dashboard"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                        <i class="fa-solid fa-shield-halved w-5 text-center"></i> Vào Trang Quản Trị
                                    </a>
                                    <div class="border-t border-gray-50 dark:border-gray-700/50 my-1"></div>
                                <?php endif; ?>

                                <a href="?action=profile"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fa-solid fa-user w-5 text-center text-gray-400"></i> Hồ sơ của tôi
                                </a>

                                <a href="?action=my_courses"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fa-solid fa-book w-5 text-center text-gray-400"></i> Khóa học của tôi
                                </a>

                                <div class="border-t border-gray-50 dark:border-gray-700/50 my-1"></div>

                                <a href="?action=logout"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Đăng xuất
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-2">
                            <a href="?action=login"
                                class="border border-gray-500 text-gray-300 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Đăng
                                nhập</a>
                            <a href="?action=register"
                                class="bg-primary hover:bg-yellow-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors shadow-lg">Đăng
                                ký</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Toggle -->
                <div class="md:hidden flex items-center gap-2">
                    <a href="?action=cart" class="relative text-gray-400 hover:text-white p-2 transition-colors group">
                        <i class="fa-solid fa-cart-shopping text-xl group-hover:scale-110 transition-transform"></i>
                        <span id="cart-badge-mobile" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full transform translate-x-1/4 -translate-y-1/4 <?= $cart_count > 0 ? '' : 'hidden' ?>">
                            <?= $cart_count ?>
                        </span>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-gray-400 hover:text-white w-10 h-10 flex items-center justify-center">
                        <i class="fa-solid text-xl" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-cloak
            class="md:hidden bg-gray-800 border-t border-gray-700">
            <div class="px-4 py-3 space-y-1">
                <a href="?action=home"
                    class="flex items-center gap-3 text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-house w-5 text-center text-gray-400"></i> Trang chủ
                </a>
                <a href="?action=courses"
                    class="flex items-center gap-3 text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-book w-5 text-center text-gray-400"></i> Khóa học
                </a>
                <a href="?action=forum"
                    class="flex items-center gap-3 text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-comments w-5 text-center text-gray-400"></i> Diễn đàn
                </a>
                <a href="?action=home#contact"
                    class="flex items-center gap-3 text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-envelope w-5 text-center text-gray-400"></i> Liên hệ
                </a>

                <!-- Dark Mode Toggle Mobile -->
                <button onclick="toggleDarkMode()"
                    class="w-full flex items-center gap-3 text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-left">
                    <i class="fa-solid fa-moon dark:hidden w-5 text-center text-gray-400"></i>
                    <i class="fa-solid fa-sun hidden dark:inline w-5 text-center text-yellow-400"></i>
                    <span class="dark:hidden">Chế độ tối</span>
                    <span class="hidden dark:inline">Chế độ sáng</span>
                </button>

                <div class="border-t border-gray-700 my-2"></div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-3 px-3 py-2">
                        <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '') ?>" alt="Avatar"
                            class="w-10 h-10 rounded-full border border-gray-500 object-cover">
                        <div>
                            <p class="text-white font-semibold text-sm">Chào,
                                <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</p>
                            <p class="text-xs text-gray-400">
                                <?= $_SESSION['user_role'] === 'admin' ? 'Quản trị viên' : 'Học viên' ?></p>
                        </div>
                    </div>

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="?action=admin_dashboard"
                            class="flex items-center gap-3 text-blue-400 hover:text-blue-300 hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            <i class="fa-solid fa-shield-halved w-5 text-center"></i> Vào Trang Quản Trị
                        </a>
                    <?php endif; ?>

                    <a href="?action=my_courses"
                        class="flex items-center gap-3 text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-book-open w-5 text-center text-gray-400"></i> Khóa học của tôi
                    </a>
                    <a href="?action=logout"
                        class="flex items-center gap-3 text-red-400 hover:text-red-300 hover:bg-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Đăng xuất
                    </a>
                <?php else: ?>
                    <div class="flex flex-col gap-2 px-3 pt-1">
                        <a href="?action=login"
                            class="text-center border border-gray-500 text-gray-300 hover:bg-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                            Đăng nhập
                        </a>
                        <a href="?action=register"
                            class="text-center bg-primary hover:bg-yellow-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors shadow-lg">
                            Đăng ký
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php $isHomePage = (!isset($_GET['action']) || $_GET['action'] === 'home'); ?>
    <main class="flex-grow w-full <?= $isHomePage ? '' : 'max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8' ?>">