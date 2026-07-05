<header class="h-16 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30 transition-colors duration-300 shrink-0">
    <div class="flex items-center gap-3">
        <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-primary transition">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block text-gray-500 dark:text-gray-400 hover:text-primary transition">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>
    
    <div class="flex items-center gap-3 sm:gap-4">
        <button @click="darkMode = !darkMode; localStorage.setItem('adminDarkMode', darkMode)" 
                class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition">
            <i class="fa-solid text-lg" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
        </button>
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 hidden sm:block">Xin chào, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
        <img src="<?= htmlspecialchars($_SESSION['user_avatar'] ?? 'https://ui-avatars.com/api/?name=Admin&background=f59e0b&color=fff') ?>" 
             class="w-9 h-9 rounded-xl border-2 border-primary object-cover shadow-sm">
    </div>
</header>
