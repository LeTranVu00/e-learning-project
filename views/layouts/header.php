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

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body
    class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 font-sans flex flex-col min-h-screen transition-colors duration-300">

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

                    <!-- Dark Mode Toggle Desktop -->
                    <button onclick="toggleDarkMode()"
                        class="text-gray-400 hover:text-yellow-400 mr-4 p-2 rounded-full hover:bg-gray-800 transition-colors"
                        title="Bật/Tắt giao diện tối">
                        <i class="fa-solid fa-moon dark:hidden text-lg"></i>
                        <i class="fa-solid fa-sun hidden dark:inline text-lg text-yellow-400"></i>
                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false"
                                class="flex items-center gap-2.5 hover:bg-gray-800 px-3 py-2 rounded-lg transition duration-200">
                                <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '') ?>" alt="Avatar"
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
                                class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-xs text-gray-400 uppercase tracking-wider">Vai trò</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        <?= $_SESSION['user_role'] === 'admin' ? 'Quản trị viên' : 'Học viên' ?>
                                    </p>
                                </div>

                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <a href="?action=admin_dashboard"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                        <i class="fa-solid fa-shield-halved w-5 text-center"></i> Vào Trang Quản Trị
                                    </a>
                                    <div class="border-t border-gray-50 my-1"></div>
                                <?php endif; ?>

                                <a href="?action=my_courses"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fa-solid fa-book w-5 text-center text-gray-400"></i> Khóa học của tôi
                                </a>

                                <div class="border-t border-gray-50 my-1"></div>

                                <a href="?action=logout"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
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
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden text-gray-400 hover:text-white w-10 h-10 flex items-center justify-center">
                    <i class="fa-solid text-xl" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
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