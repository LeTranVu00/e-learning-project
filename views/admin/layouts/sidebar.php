<?php
$currentAction = $_GET['action'] ?? 'admin_dashboard';
?>
<!-- Mobile Overlay -->
<div x-show="mobileSidebarOpen" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak
    class="fixed inset-0 bg-black/50 z-30 lg:hidden" @click="mobileSidebarOpen = false"></div>

<!-- Sidebar -->
<aside
    class="bg-dark text-white transition-all duration-300 flex flex-col shadow-2xl fixed lg:sticky top-0 left-0 h-screen z-40 w-64 -translate-x-full lg:translate-x-0"
    :class="{'translate-x-0': mobileSidebarOpen, '-translate-x-full': !mobileSidebarOpen, 'w-64': true, 'lg:w-64': sidebarOpen, 'lg:!w-20': !sidebarOpen}">
    <div class="h-16 flex items-center justify-center border-b border-gray-800 shrink-0">
        <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
        <span x-show="sidebarOpen"
            class="ml-3 font-bold text-lg tracking-wider transition-opacity duration-300 hidden lg:inline">ADMIN
            PANEL</span>
    </div>

    <nav class="flex-1 px-2 py-6 space-y-2 overflow-y-auto">
        <a href="?action=admin_dashboard"
            class="flex items-center px-4 py-3 <?= $currentAction == 'admin_dashboard' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group">
            <i class="fa-solid fa-chart-pie w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium hidden lg:inline">Tổng quan</span>
        </a>

        <a href="?action=admin_manage_courses"
            class="flex items-center px-4 py-3 <?= $currentAction == 'admin_manage_courses' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group">
            <i class="fa-solid fa-book-open w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium hidden lg:inline">Quản lý Khóa học</span>
        </a>

        <a href="?action=admin_manage_categories"
            class="flex items-center px-4 py-3 <?= $currentAction == 'admin_manage_categories' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group">
            <i class="fa-solid fa-folder-tree w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium hidden lg:inline">Quản lý Danh mục</span>
        </a>

        <a href="?action=admin_manage_comments"
            class="flex items-center px-4 py-3 <?= $currentAction == 'admin_manage_comments' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group">
            <i class="fa-solid fa-comments w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium hidden lg:inline">Quản lý Bình luận</span>
        </a>

        <a href="?action=admin_manage_users"
            class="flex items-center px-4 py-3 <?= $currentAction == 'admin_manage_users' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group">
            <i class="fa-solid fa-users w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium hidden lg:inline">Người dùng</span>
        </a>

    </nav>

    <div class="p-4 border-t border-gray-800 shrink-0">
        <a href="?action=home"
            class="flex items-center px-4 py-3 text-gray-400 hover:bg-red-500 hover:text-white rounded-xl transition">
            <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium hidden lg:inline">Thoát Admin</span>
        </a>
    </div>
</aside>