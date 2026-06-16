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
            theme: {
                extend: {
                    colors: { primary: '#f59e0b', dark: '#111827' }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

<nav x-data="{ mobileMenuOpen: false }" class="bg-dark text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <a href="?action=home" class="flex items-center text-xl font-bold tracking-wider">
                <i class="fa-solid fa-graduation-cap text-primary mr-2 text-2xl"></i>E-LEARNING
            </a>
            
            <div class="hidden md:flex space-x-8 items-center">
                <a href="?action=home" class="text-gray-300 hover:text-primary transition px-3 py-2 font-medium">Trang chủ</a>
                <a href="#" class="text-gray-300 hover:text-primary transition px-3 py-2 font-medium">Khóa học</a>
                
                <div class="flex items-center ml-4 border-l border-gray-700 pl-6">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center gap-2 hover:bg-gray-800 p-2 rounded-lg transition duration-200">
                                <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '') ?>" alt="Avatar" class="w-8 h-8 rounded-full border border-gray-500 object-cover">
                                <span class="text-sm font-semibold text-gray-200">
                                    Chào, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform" :class="profileOpen ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="profileOpen" x-collapse x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm text-gray-500">Vai trò:</p>
                                    <p class="text-sm font-bold text-gray-900 truncate"><?= $_SESSION['user_role'] === 'admin' ? 'Quản trị viên' : 'Học viên' ?></p>
                                </div>
                                <a href="?action=my_courses" class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-primary transition">
                                    <i class="fa-solid fa-book w-5"></i> Khóa học của tôi
                                </a>
                                <a href="?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition mt-1 border-t border-gray-100 pt-2">
                                    <i class="fa-solid fa-right-from-bracket w-5"></i> Đăng xuất
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        
                        <div class="flex space-x-3">
                            <a href="?action=login" class="border border-gray-500 text-gray-300 hover:bg-gray-700 px-4 py-2 rounded-md text-sm font-medium transition">Đăng nhập</a>
                            <a href="?action=register" class="bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium transition shadow-lg">Đăng ký</a>
                        </div>
                        
                    <?php endif; ?>
                </div>
            </div>

            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-400 hover:text-white focus:outline-none">
                    <i class="fa-solid" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'" style="font-size: 1.5rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden bg-gray-800 border-t border-gray-700" x-collapse x-cloak>
        <div class="px-4 pt-2 pb-4 space-y-2">
            <a href="?action=home" class="block text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2 rounded-md font-medium">Trang chủ</a>
            <a href="#" class="block text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2 rounded-md font-medium">Khóa học</a>
            <hr class="border-gray-700 my-2">

            <?php if(isset($_SESSION['user_id'])): ?>
                
                <div class="flex items-center gap-3 px-3 py-2">
                    <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? '') ?>" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-500 object-cover">
                    <div>
                        <p class="text-white font-semibold">Chào, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</p>
                        <p class="text-xs text-gray-400"><?= $_SESSION['user_role'] === 'admin' ? 'Quản trị viên' : 'Học viên' ?></p>
                    </div>
                </div>
                <a href="?action=my_courses" class="block text-gray-300 hover:text-white hover:bg-gray-700 px-3 py-2 rounded-md font-medium">
                    <i class="fa-solid fa-book w-5 mr-1"></i> Khóa học của tôi
                </a>
                <a href="?action=logout" class="block text-red-400 hover:text-red-300 hover:bg-gray-700 px-3 py-2 rounded-md font-medium">
                    <i class="fa-solid fa-right-from-bracket w-5 mr-1"></i> Đăng xuất
                </a>

            <?php else: ?>
                
                <div class="flex flex-col space-y-2 px-3 pt-2">
                    <a href="?action=login" class="text-center border border-gray-500 text-gray-300 hover:bg-gray-700 px-4 py-2 rounded-md text-sm font-medium transition">Đăng nhập</a>
                    <a href="?action=register" class="text-center bg-primary hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium transition shadow-lg">Đăng ký</a>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-grow w-full max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">