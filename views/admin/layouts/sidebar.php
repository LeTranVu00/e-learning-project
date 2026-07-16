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
    <div class="h-16 flex items-center pl-7 border-b border-gray-800 shrink-0 overflow-hidden">
        <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
        <span x-show="sidebarOpen"
            class="ml-3 font-bold text-lg tracking-wider transition-opacity duration-300 whitespace-nowrap">ADMIN
            PANEL</span>
    </div>

    <nav class="flex-1 px-2 py-6 space-y-2 overflow-y-auto">
        <a href="?action=admin_dashboard"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_dashboard' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-chart-pie w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Tổng quan</span>
        </a>

        <a href="?action=admin_manage_courses"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_manage_courses' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-book-open w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Quản lý Khóa học</span>
        </a>

        <a href="?action=admin_manage_categories"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_manage_categories' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-folder-tree w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Quản lý Danh mục</span>
        </a>

        <a href="?action=admin_manage_comments"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_manage_comments' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-comments w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Quản lý Bình luận</span>
        </a>

        <a href="?action=admin_manage_contacts"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_manage_contacts' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-envelope-open-text w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Quản lý Liên hệ</span>
        </a>

        <a href="?action=admin_manage_users"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_manage_users' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-users w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Người dùng</span>
        </a>

        <a href="?action=admin_system_logs"
            class="flex items-center px-5 py-3 <?= $currentAction == 'admin_system_logs' ? 'bg-gray-800 text-primary' : 'text-gray-400 hover:bg-gray-800 hover:text-white' ?> rounded-xl transition group overflow-hidden">
            <i class="fa-solid fa-clipboard-list w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Nhật ký hệ thống</span>
        </a>

    </nav>

    <div class="p-4 border-t border-gray-800 shrink-0">
        <a href="?action=home"
            class="flex items-center px-5 py-3 text-gray-400 hover:bg-red-500 hover:text-white rounded-xl transition overflow-hidden">
            <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center shrink-0"></i>
            <span x-show="sidebarOpen" class="ml-3 font-medium whitespace-nowrap transition-opacity duration-300" x-transition.opacity.duration.300ms>Thoát Admin</span>
        </a>
    </div>
</aside>