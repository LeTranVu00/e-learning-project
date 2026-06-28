<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Khóa học - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style> 
        .ck-editor__editable_inline { min-height: 250px; } 
        .ck.ck-balloon-panel { z-index: 99999 !important; } 
        
        /* === MA THUẬT BO TRÒN BẢNG TRONG CKEDITOR === */
        .ck-content figure.table {
            border-radius: 0.75rem !important; /* Bo tròn góc */
            overflow: hidden !important;       /* Cắt bỏ phần góc nhọn của ô bên trong */
            border: 1px solid #e5e7eb !important; /* Màu viền xám nhạt (Tailwind gray-200) */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* Thêm bóng đổ nhẹ */
        }
        .ck-content figure.table table {
            margin: 0 !important;
            border: none !important; /* Ẩn viền mặc định để không bị đè 2 lớp viền */
        }
        .ck-content figure.table th,
        .ck-content figure.table td {
            border-color: #e5e7eb !important; /* Đổi viền các ô bên trong cho đồng bộ */
            padding: 10px !important; /* Cho ô thoáng ra một chút */
        }
    </style>
    <script> tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } } </script>
</head>
<body class="bg-gray-100 font-sans flex min-h-screen" 
      x-data="{ 
          sidebarOpen: true, 
          showAddModal: false, 
          showEditModal: false,
          editData: { id: '', title: '', price: 0, old_thumbnail: '' },
          // Hàm này chạy khi bấm nút Sửa
          openEdit(course) {
              this.editData.id = course.id;
              this.editData.title = course.title;
              this.editData.price = course.price || 0;
              this.editData.old_thumbnail = course.thumbnail;
              this.showEditModal = true;
              
              if(window.editorEdit) { window.editorEdit.setData(course.description || ''); }
              if(window.editorEditBenefits) { window.editorEditBenefits.setData(course.benefits || ''); }
              if(window.editorEditReqs) { window.editorEditReqs.setData(course.requirements || ''); }
          }
      }">

    <aside class="w-64 bg-dark text-white transition-all duration-300 flex flex-col shadow-2xl relative z-20" :class="sidebarOpen ? '' : '!w-20'">
        <div class="h-16 flex items-center justify-center border-b border-gray-800">
            <i class="fa-solid fa-graduation-cap text-primary text-2xl"></i>
            <span x-show="sidebarOpen" class="ml-3 font-bold text-lg tracking-wider">ADMIN PANEL</span>
        </div>
        <nav class="flex-1 px-2 py-6 space-y-2">
            <a href="?action=admin_dashboard" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-chart-pie w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Tổng quan</span>
            </a>
            <a href="?action=admin_manage_courses" class="flex items-center px-4 py-3 bg-gray-800 text-primary rounded-xl transition group">
                <i class="fa-solid fa-book-open w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Quản lý Khóa học</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-800 hover:text-white rounded-xl transition group">
                <i class="fa-solid fa-users w-6 text-center"></i>
                <span x-show="sidebarOpen" class="ml-3 font-medium">Học viên</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-800">
            <a href="?action=home" class="flex items-center px-4 py-3 text-gray-400 hover:bg-red-500 hover:text-white rounded-xl transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center"></i><span x-show="sidebarOpen" class="ml-3 font-medium">Thoát Admin</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-primary focus:outline-none"><i class="fa-solid fa-bars text-xl"></i></button>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">Xin chào, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Quản lý Khóa học</h1>
                </div>
                <button @click="showAddModal = true" class="bg-dark hover:bg-gray-800 text-white font-medium py-2.5 px-6 rounded-xl transition shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Thêm Khóa Học
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider">
                            <th class="p-4 font-semibold">Hình ảnh</th>
                            <th class="p-4 font-semibold">Tên khóa học</th>
                            <th class="p-4 font-semibold text-center w-64">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(!empty($courses)): foreach($courses as $course): ?>
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-4">
                                <img src="<?= htmlspecialchars($course['thumbnail'] ?? 'https://placehold.co/100x70?text=No+Image') ?>" class="w-24 h-16 object-cover rounded-lg border border-gray-200">
                            </td>
                            <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($course['title']) ?></td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="?action=admin_manage_content&id=<?= $course['id'] ?>" title="Nội dung bài giảng" class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition shadow-sm border border-blue-100">
                                        <i class="fa-solid fa-list-check"></i>
                                    </a>
                                    <button @click="openEdit(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)" title="Sửa khóa học" class="w-10 h-10 rounded-full flex items-center justify-center bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition shadow-sm border border-yellow-100">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="?action=admin_delete_course&id=<?= $course['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn XÓA khóa học này không? Mọi bài giảng bên trong sẽ bị mất!');" title="Xóa khóa học" class="w-10 h-10 rounded-full flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition shadow-sm border border-red-100">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="3" class="p-4 text-center text-gray-500">Chưa có khóa học nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showAddModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showAddModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:p-0">
            <div x-show="showAddModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-4xl z-50 flex flex-col max-h-[90vh]">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold">Thêm khóa học mới</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=admin_store_course" method="POST" id="formAdd" class="space-y-6" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Tên khóa học <span class="text-red-500">*</span></label>
                                <input type="text" name="title" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Giá khóa học (VNĐ) <span class="text-red-500">*</span></label>
                                <input type="number" name="price" required min="0" value="0" step="1000" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Ảnh bìa khóa học <span class="text-red-500">*</span></label>
                            <input type="file" name="thumbnail" accept="image/*" required class="w-full px-4 py-2 border rounded-xl outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Mô tả</label>
                            <textarea name="description" id="editor_add"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Bạn sẽ học được gì?</label>
                            <textarea name="benefits" id="editor_add_benefits"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Yêu cầu khóa học</label>
                            <textarea name="requirements" id="editor_add_reqs"></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                    <button @click="showAddModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200">Hủy</button>
                    <button type="submit" form="formAdd" class="bg-dark text-white font-bold py-2.5 px-6 rounded-xl">Lưu mới</button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="showEditModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" @click="showEditModal = false"></div>
        <div class="flex items-center justify-center min-h-screen px-4 py-8 text-center sm:p-0">
            <div x-show="showEditModal" class="relative bg-white rounded-2xl text-left shadow-2xl w-full max-w-4xl z-50 flex flex-col max-h-[90vh]">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                    <h3 class="text-xl font-bold">Sửa khóa học</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="px-6 py-6 overflow-y-auto grow">
                    <form action="?action=admin_update_course" method="POST" id="formEdit" class="space-y-6" enctype="multipart/form-data">
                        <input type="hidden" name="id" :value="editData.id">
                        <input type="hidden" name="old_thumbnail" :value="editData.old_thumbnail">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Tên khóa học <span class="text-red-500">*</span></label>
                                <input type="text" name="title" x-model="editData.title" required class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Giá khóa học (VNĐ) <span class="text-red-500">*</span></label>
                                <input type="number" name="price" x-model="editData.price" required min="0" step="1000" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Ảnh bìa (Bỏ trống nếu không muốn đổi)</label>
                            <input type="file" name="thumbnail" accept="image/*" class="w-full px-4 py-2 border rounded-xl outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2">Mô tả</label>
                            <textarea name="description" id="editor_edit"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2 text-gray-700">Bạn sẽ học được gì?</label>
                            <textarea name="benefits" id="editor_edit_benefits"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2 text-gray-700">Yêu cầu khóa học</label>
                            <textarea name="requirements" id="editor_edit_reqs"></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                    <button @click="showEditModal = false" class="px-6 py-2.5 rounded-xl bg-gray-200">Hủy</button>
                    <button type="submit" form="formEdit" class="bg-primary text-white font-bold py-2.5 px-6 rounded-xl">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
            const config = { toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ] };
            
            // Khởi tạo cho Form Thêm
            ClassicEditor.create(document.querySelector('#editor_add'), config).catch(err => console.error(err));
            ClassicEditor.create(document.querySelector('#editor_add_benefits'), config).catch(err => console.error(err));
            ClassicEditor.create(document.querySelector('#editor_add_reqs'), config).catch(err => console.error(err));
            
            // Khởi tạo cho Form Sửa
            ClassicEditor.create(document.querySelector('#editor_edit'), config).then(editor => { window.editorEdit = editor; }).catch(err => console.error(err));
            ClassicEditor.create(document.querySelector('#editor_edit_benefits'), config).then(editor => { window.editorEditBenefits = editor; }).catch(err => console.error(err));
            ClassicEditor.create(document.querySelector('#editor_edit_reqs'), config).then(editor => { window.editorEditReqs = editor; }).catch(err => console.error(err));
        });
    </script>
</body>
</html>