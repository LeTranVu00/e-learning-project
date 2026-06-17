<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        /* Sửa lại z-index để mấy cái tool của CKEditor không bị Modal đè lên */
        .ck-editor__editable_inline { min-height: 250px; }
        .ck.ck-balloon-panel { z-index: 99999 !important; }
    </style>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } }
    </script>
</head>
<body class="bg-gray-100 font-sans flex min-h-screen" x-data="{ sidebarOpen: true, showModal: false }">

    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-dark text-white transition-all duration-300 flex flex-col shadow-2xl relative z-20">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-lg tracking-wider transition-opacity duration-300">ADMIN PANEL</span>
        </div>

        <nav class="flex-1 px-2 py-6 space-y-2">
            <a href="?action=admin_dashboard" class="flex items-center px-4 py-3 bg-gray-800 text-primary rounded-xl transition group">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Tổng quan</span>
            </a>
            
            <a href="#" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
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
                        <p class="text-2xl font-bold text-gray-800">12</p>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-blue-500">
                    <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-2xl">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Tổng Học Viên</p>
                        <p class="text-2xl font-bold text-gray-800">1,250</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 border-l-4 border-l-green-500">
                    <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center text-green-500 text-2xl">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Lượt Ghi Danh</p>
                        <p class="text-2xl font-bold text-gray-800">3,480</p>
                    </div>
                </div>
            </div>

            <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Quản lý nội dung</h2>
                    <p class="text-sm text-gray-500">Thêm mới và chỉnh sửa các khóa học trên hệ thống.</p>
                </div>
                
                <button @click="showModal = true" class="bg-dark hover:bg-gray-800 text-white font-medium py-2.5 px-6 rounded-xl transition shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Thêm Khóa Học Mới
                </button>
            </div>
        </div>
    </main>
<div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showModal = false"></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl w-full z-50 flex flex-col max-h-[90vh]">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold text-gray-800" id="modal-title">Thêm khóa học mới</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded-lg transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=admin_store_course" method="POST" id="createCourseForm" class="space-y-6" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tên khóa học <span class="text-red-500">*</span></label>
                                <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh bìa khóa học <span class="text-red-500">*</span></label>
                                <input type="file" name="thumbnail" accept="image/*" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-primary hover:file:bg-yellow-100">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung chi tiết khóa học</label>
                                <textarea name="description" id="course_description_modal"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 shrink-0">
                    <button type="button" @click="showModal = false" class="px-6 py-2.5 rounded-xl text-gray-600 font-medium hover:bg-gray-200 transition">
                        Hủy bỏ
                    </button>
                    <button type="submit" form="createCourseForm" class="bg-primary hover:bg-yellow-600 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu khóa học
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            ClassicEditor
                .create(document.querySelector('#course_description_modal'), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
</body>
</html>