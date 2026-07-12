<?php $pageTitle = 'Quản lý Danh mục'; require_once 'layouts/header.php'; ?>

            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý Danh mục</h1>
                    <p class="text-gray-500 mt-1">Quản lý các danh mục khóa học trên hệ thống.</p>
                </div>
                <button onclick="openCreateModal()" class="bg-primary hover:bg-yellow-600 text-white px-6 py-2.5 rounded-xl font-medium transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Thêm danh mục
                </button>
            </div>

            <!-- Toast Notification -->
            <?php if (isset($_SESSION['success'])): ?>
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-init="setTimeout(() => show = false, 3000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-full"
                     class="fixed top-20 right-6 z-50 flex items-center bg-white border-l-4 border-green-500 rounded-xl shadow-xl p-4 min-w-[300px]">
                    <div class="text-green-500 mr-3">
                        <i class="fa-solid fa-circle-check text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Thành công!</h4>
                        <p class="text-sm text-gray-600"><?= $_SESSION['success'] ?></p>
                    </div>
                    <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Bảng Danh mục -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="overflow-x-auto"><table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500 uppercase">
                                <th class="py-4 px-6 font-semibold">Icon</th>
                                <th class="py-4 px-6 font-semibold">Tên Danh mục</th>
                                <th class="py-4 px-6 font-semibold">Ngày tạo</th>
                                <th class="py-4 px-6 font-semibold text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150 group">
                                        <td class="py-4 px-6 w-24">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center <?= htmlspecialchars($cat['color']) ?>">
                                                <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?>"></i>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-gray-900"><?= htmlspecialchars($cat['name']) ?></div>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500 text-sm font-medium">
                                            <?= date('d/m/Y', strtotime($cat['created_at'] ?? 'now')) ?>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <a href="?action=admin_manage_category_courses&id=<?= $cat['id'] ?>" class="text-blue-600 bg-blue-50 hover:bg-blue-500 hover:text-white p-2 rounded-lg transition shadow-sm border border-blue-100 flex items-center justify-center" title="Xem chi tiết khóa học">
                                                    <i class="fa-regular fa-eye"></i>
                                                </a>
                                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($cat)) ?>)" class="text-yellow-600 bg-yellow-50 hover:bg-yellow-500 hover:text-white p-2 rounded-lg transition shadow-sm border border-yellow-100" title="Sửa">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>
                                                <button onclick="confirmDelete(<?= $cat['id'] ?>)" class="text-red-600 bg-red-50 hover:bg-red-500 hover:text-white p-2 rounded-lg transition shadow-sm border border-red-100" title="Xóa">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-gray-500">Chưa có danh mục nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table></div>
                </div>
            </div>

            <!-- Modal Thêm/Sửa Danh mục -->
            <div id="categoryModal" class="fixed inset-0 z-[100] hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
                
                <!-- Modal Content -->
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full border border-gray-100">
                        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                            <h3 class="text-xl font-bold flex items-center gap-2" id="modalTitle"><i class="fa-solid fa-plus text-primary"></i> Thêm Danh mục mới</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="bg-white px-6 py-6">
                            <form id="categoryForm" action="?action=admin_store_category" method="POST">
                                <input type="hidden" name="id" id="categoryId">
                                
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tên Danh mục <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" id="categoryName" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:bg-white outline-none transition shadow-sm">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">FontAwesome Icon <span class="text-gray-400 font-normal">(vd: fa-laptop-code)</span></label>
                                        <input type="text" name="icon" id="categoryIcon" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:bg-white outline-none transition shadow-sm">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Màu sắc</label>
                                        <select name="color" id="categoryColor" required class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:bg-white outline-none transition shadow-sm appearance-none cursor-pointer">
                                            <option value="bg-gray-100 text-gray-600">Xám (Gray)</option>
                                            <option value="bg-blue-100 text-blue-600">Xanh dương (Blue)</option>
                                            <option value="bg-green-100 text-green-600">Xanh lá (Green)</option>
                                            <option value="bg-red-100 text-red-600">Đỏ (Red)</option>
                                            <option value="bg-yellow-100 text-yellow-600">Vàng (Yellow)</option>
                                            <option value="bg-purple-100 text-purple-600">Tím (Purple)</option>
                                            <option value="bg-pink-100 text-pink-600">Hồng (Pink)</option>
                                            <option value="bg-indigo-100 text-indigo-600">Chàm (Indigo)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition">
                                        Hủy
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 bg-dark text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-sm flex items-center gap-2">
                                        <i class="fa-solid fa-floppy-disk"></i> Lưu lại
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                const modal = document.getElementById('categoryModal');
                const form = document.getElementById('categoryForm');
                const title = document.getElementById('modalTitle');
                const inputId = document.getElementById('categoryId');
                const inputName = document.getElementById('categoryName');
                const inputIcon = document.getElementById('categoryIcon');
                const inputColor = document.getElementById('categoryColor');

                function openCreateModal() {
                    form.action = "?action=admin_store_category";
                    title.innerHTML = '<i class="fa-solid fa-plus text-primary"></i> Thêm Danh mục mới';
                    inputId.value = '';
                    inputName.value = '';
                    inputIcon.value = 'fa-folder';
                    inputColor.value = 'bg-gray-100 text-gray-600';
                    modal.classList.remove('hidden');
                }

                function openEditModal(category) {
                    form.action = "?action=admin_update_category";
                    title.innerHTML = '<i class="fa-solid fa-pen-to-square text-yellow-500"></i> Sửa Danh mục';
                    inputId.value = category.id;
                    inputName.value = category.name;
                    inputIcon.value = category.icon;
                    inputColor.value = category.color;
                    modal.classList.remove('hidden');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                }

                function confirmDelete(id) {
                    Swal.fire({
                        title: 'Xóa danh mục?',
                        text: 'Bạn có chắc chắn muốn xóa danh mục này? Khóa học sẽ không bị xóa mà chỉ mất liên kết.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "?action=admin_delete_category&id=" + id;
                        }
                    });
                }
            </script>

        <?php require_once 'layouts/footer.php'; ?>

