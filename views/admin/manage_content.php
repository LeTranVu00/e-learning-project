<?php $pageTitle = 'Quản lý Nội dung';
require_once 'layouts/header.php'; ?>
<div class="max-w-4xl mx-auto" x-data="{ 
        showChapterModal: false, 
        showMaterialModal: false, 
        showEditChapterModal: false,
        showEditMaterialModal: false,
        currentChapterId: null,
        newMaterialType: 'file', 
        addFiles: [],
        addLinks: [],
        editFileName: '',
        editChapterData: { id: '', title: '', description: '' },
        editMaterialData: { id: '', title: '', type: '', content: '', description: '' },

        openEditChapter(id, title, description) {
            this.editChapterData.id = id;
            this.editChapterData.title = title;
            this.editChapterData.description = description || '';
            if (window.editorEditChapter) {
                window.editorEditChapter.setData(this.editChapterData.description);
            }
            this.showEditChapterModal = true;
        },
        openEditMaterial(id, title, type, content, description) {
            this.editMaterialData.id = id;
            this.editMaterialData.title = title;
            this.editMaterialData.type = type;
            this.editMaterialData.content = content;
            this.editMaterialData.description = description || '';
            this.editFileName = '';
            if (window.editorEditMaterial) {
                window.editorEditMaterial.setData(this.editMaterialData.description);
            }
            this.showEditMaterialModal = true;
        }
    }">

    <!-- Toast Notification -->
    <?php if (isset($_SESSION['success'])): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
            class="fixed top-20 right-6 z-50 flex items-center bg-white dark:bg-gray-800 border-l-4 border-green-500 rounded-xl shadow-xl p-4 min-w-[300px]">
            <div class="text-green-500 mr-3"><i class="fa-solid fa-circle-check text-2xl"></i></div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Thành công</p>
                <p class="text-gray-800 dark:text-white font-bold"><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
            class="fixed top-20 right-6 z-50 flex items-center bg-white dark:bg-gray-800 border-l-4 border-red-500 rounded-xl shadow-xl p-4 min-w-[300px]">
            <div class="text-red-500 mr-3"><i class="fa-solid fa-circle-exclamation text-2xl"></i></div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Lỗi</p>
                <p class="text-gray-800 dark:text-white font-bold"><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="mb-6 flex justify-between items-center">
        <a href="?action=admin_manage_courses"
            class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Quản lý khóa học
        </a>
        <button @click="showChapterModal = true"
            class="bg-dark hover:bg-gray-800 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-lg flex items-center gap-2">
            <i class="fa-solid fa-folder-plus"></i> Thêm Chương Mới
        </button>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 border-b dark:border-gray-700 pb-4">
        Khóa học: <span class="text-primary"><?= htmlspecialchars($course['title']) ?></span>
    </h1>

    <?php if (empty($curriculum)): ?>
        <div
            class="text-center bg-white dark:bg-gray-800 dark:text-white p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            Khóa học này chưa có chương nào!</div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($curriculum as $chapter): ?>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

                    <div
                        class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-200 dark:border-gray-700 gap-3">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <i class="fa-solid fa-list mr-2 text-gray-400"></i> <?= htmlspecialchars($chapter['title']) ?>
                        </h2>

                        <div class="flex items-center gap-2">
                            <button
                                @click="currentChapterId = <?= $chapter['id'] ?>; addFiles = []; addLinks = []; document.getElementById('dropzone-file-add').value = ''; showMaterialModal = true"
                                title="Thêm bài giảng"
                                class="text-sm bg-primary/10 text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg transition font-medium">
                                <i class="fa-solid fa-plus mr-1"></i> Thêm bài
                            </button>
                            <button
                                @click="openEditChapter(<?= $chapter['id'] ?>, '<?= htmlspecialchars(addslashes($chapter['title'])) ?>', '<?= htmlspecialchars(addslashes($chapter['description'] ?? '')) ?>')"
                                title="Sửa Chương"
                                class="w-8 h-8 rounded-full flex items-center justify-center bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </button>
                            <a href="?action=admin_delete_chapter&id=<?= $chapter['id'] ?>&course_id=<?= $course['id'] ?>"
                                onclick="return confirm('Xóa chương này sẽ mất toàn bộ bài giảng bên trong. Bạn chắc chứ?');"
                                title="Xóa Chương"
                                class="w-8 h-8 rounded-full flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <div class="p-6">
                        <?php if (empty($chapter['materials'])): ?>
                            <p class="text-gray-400 text-sm italic">Chưa có bài giảng nào.</p>
                        <?php else: ?>
                            <ul class="space-y-3">
                                <?php foreach ($chapter['materials'] as $material): ?>
                                    <li
                                        class="flex items-center justify-between border border-gray-100 dark:border-gray-700 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-white dark:hover:bg-gray-700 hover:border-gray-300 transition group">
                                        <a href="<?= htmlspecialchars($material['content']) ?>" target="_blank"
                                            class="font-medium text-gray-700 dark:text-gray-300 dark:hover:text-primary hover:text-primary flex items-center gap-2 truncate pr-4 transition cursor-pointer">
                                            <i
                                                class="fa-solid <?= $material['type'] == 'file' ? 'fa-file-pdf text-red-500' : 'fa-circle-play text-blue-500' ?>"></i>
                                            <span class="truncate"><?= htmlspecialchars($material['title']) ?></span>
                                        </a>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <span
                                                class="text-[10px] uppercase bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-200 px-2 py-1 rounded font-bold mr-2"><?= $material['type'] ?></span>
                                            <button
                                                @click="openEditMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars(addslashes($material['title'])) ?>', '<?= $material['type'] ?>', '<?= htmlspecialchars(addslashes($material['content'])) ?>', '<?= htmlspecialchars(addslashes($material['description'] ?? '')) ?>')"
                                                title="Sửa bài"
                                                class="w-7 h-7 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 hover:bg-yellow-500 hover:text-white transition opacity-0 group-hover:opacity-100">
                                                <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                            </button>
                                            <a href="?action=admin_delete_material&id=<?= $material['id'] ?>&course_id=<?= $course['id'] ?>"
                                                onclick="return confirm('Xóa bài giảng này?');" title="Xóa bài"
                                                class="w-7 h-7 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 hover:bg-red-500 hover:text-white transition opacity-0 group-hover:opacity-100">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div x-show="showChapterModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-2xl p-6 relative"
            @click.away="showChapterModal = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Thêm Chương mới</h3>
            <form action="?action=admin_store_chapter" method="POST">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Tên Chương</label>
                    <input type="text" name="title" required placeholder="VD: Chương 1 - Nhập môn"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Mô tả chương (Tùy chọn)</label>
                    <textarea name="description" id="editor-add-chapter"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="showChapterModal = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-xl font-medium transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-dark text-white rounded-xl font-medium">Lưu
                        Chương</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditChapterModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-2xl p-6 relative"
            @click.away="showEditChapterModal = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Sửa tên Chương</h3>
            <form action="?action=admin_update_chapter" method="POST">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="hidden" name="id" :value="editChapterData.id">
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Tên Chương</label>
                    <input type="text" name="title" x-model="editChapterData.title" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-primary outline-none transition">
                </div>
                <div class="mb-4" wire:ignore>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Mô tả chương (Tùy chọn)</label>
                    <textarea name="description" id="editor-edit-chapter"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="showEditChapterModal = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-xl font-medium transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl font-medium">Lưu thay
                        đổi</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showMaterialModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-2xl p-6 relative"
            @click.away="showMaterialModal = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Thêm Bài giảng</h3>

            <form action="?action=admin_store_material" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="hidden" name="chapter_id" :value="currentChapterId">

                <div>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Nội dung chi tiết / Mô tả (Tùy chọn - Áp dụng chung)</label>
                    <textarea name="description" id="editor-add-material"></textarea>
                </div>

                <!-- Khu vực tải file -->
                <div>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Tải file lên (Mỗi file = 1 Bài giảng)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file-add"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-gray-500">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl mb-2 text-primary"></i>
                                <p class="text-sm"><span class="font-semibold">Nhấn để quét chọn nhiều file</span></p>
                                <p class="text-[10px] text-gray-400 mt-1">Hỗ trợ mọi định dạng (Max: 10MB)</p>
                            </div>
                            <input id="dropzone-file-add" type="file" name="slide_files[]" multiple
                                class="hidden" @change="addFiles = Array.from($event.target.files)" />
                        </label>
                    </div>
                    
                    <!-- Hiển thị danh sách file đã chọn -->
                    <div x-show="addFiles.length > 0" x-cloak class="mt-3 space-y-2 max-h-40 overflow-y-auto pr-2">
                        <template x-for="(file, index) in addFiles" :key="index">
                            <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-2 rounded-lg border border-gray-200 dark:border-gray-600">
                                <i class="fa-solid fa-file-circle-check text-green-500"></i> 
                                <span x-text="file.name" class="font-medium truncate flex-1"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Khu vực thêm link -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold dark:text-gray-300">Đường dẫn ngoài (Mỗi link = 1 Bài giảng)</label>
                        <button type="button" @click="addLinks.push({title: '', url: ''})" 
                                class="text-xs font-bold bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <i class="fa-solid fa-plus"></i> Thêm Link
                        </button>
                    </div>
                    
                    <div class="space-y-3 max-h-48 overflow-y-auto pr-2" x-show="addLinks.length > 0" x-cloak>
                        <template x-for="(link, index) in addLinks" :key="index">
                            <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700 p-3 rounded-xl border border-gray-200 dark:border-gray-600">
                                <div class="flex-1 space-y-3">
                                    <input type="text" x-model="link.title" :name="'link_titles['+index+']'" placeholder="Tên bài giảng cho link này..." required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-white rounded-lg outline-none focus:ring-2 focus:ring-primary transition">
                                    <input type="url" x-model="link.url" :name="'link_urls['+index+']'" placeholder="https://youtube.com/..." required
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 dark:text-white rounded-lg outline-none focus:ring-2 focus:ring-primary transition">
                                </div>
                                <button type="button" @click="addLinks.splice(index, 1)" 
                                        class="w-8 h-8 flex shrink-0 items-center justify-center bg-red-100 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition" title="Xóa link">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p x-show="addLinks.length === 0" class="text-sm text-gray-400 italic mt-1">Chưa có link nào được thêm.</p>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="showMaterialModal = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-xl font-medium transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-dark text-white rounded-xl font-medium">Lưu Bài
                        giảng</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditMaterialModal" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-2xl p-6 relative"
            @click.away="showEditMaterialModal = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90">
            <h3 class="text-xl font-bold mb-4 dark:text-white">Sửa Bài giảng</h3>

            <form action="?action=admin_update_material" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="hidden" name="id" :value="editMaterialData.id">

                <div>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Tên bài giảng</label>
                    <input type="text" name="title" x-model="editMaterialData.title" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-xl outline-none focus:ring-2 focus:ring-primary transition">
                </div>

                <div wire:ignore>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Nội dung chi tiết / Mô tả (Tùy
                        chọn)</label>
                    <textarea name="description" id="editor-edit-material"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Loại định dạng</label>
                    <select name="type" x-model="editMaterialData.type"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-xl outline-none focus:ring-2 focus:ring-primary transition">
                        <option value="file">📂 Tải lên từ thiết bị (Tất cả định dạng)</option>
                        <option value="link">🔗 Đường dẫn bên ngoài (YouTube, GG Drive...)</option>
                        <option value="video" x-show="editMaterialData.type === 'video'">🎥 Video (Dữ liệu cũ)</option>
                    </select>
                </div>

                <div x-show="editMaterialData.type === 'video' || editMaterialData.type === 'link'" x-collapse>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">Đường dẫn (URL)</label>
                    <input type="text" name="content" x-model="editMaterialData.content"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-xl outline-none focus:ring-2 focus:ring-primary transition">
                </div>

                <div x-show="editMaterialData.type === 'file'" x-collapse>
                    <label class="block text-sm font-semibold mb-2 dark:text-gray-300">File hiện tại:</label>
                    <a :href="editMaterialData.content" target="_blank"
                        class="text-sm text-blue-500 hover:underline mb-3 block truncate">
                        <i class="fa-solid fa-download mr-1"></i> <span x-text="editMaterialData.content"></span>
                    </a>

                    <label class="block text-sm font-semibold mb-2 text-gray-600">Tải file mới (Bỏ qua nếu giữ
                        nguyên)</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file-edit"
                            class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-gray-500">
                                <i class="fa-solid fa-arrow-up-from-bracket mb-1 text-primary"></i>
                                <p class="text-xs">Nhấn để chọn file thay thế</p>
                            </div>
                            <input id="dropzone-file-edit" type="file" name="slide_file"
                                class="hidden" @change="editFileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                        </label>
                    </div>
                    <div x-show="editFileName" x-cloak class="mt-2 text-sm text-gray-600 dark:text-gray-300 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-circle-check text-green-500"></i> Đã chọn thay thế: <span x-text="editFileName" class="font-medium truncate max-w-xs"></span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="showEditMaterialModal = false"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-xl font-medium transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl font-medium">Lưu thay
                        đổi</button>
                </div>
            </form>
        </div>
    </div>

</div>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const config = {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
        };
        if (document.querySelector('#editor-add-chapter')) {
            ClassicEditor.create(document.querySelector('#editor-add-chapter'), config).catch(error => { console.error(error); });
        }
        if (document.querySelector('#editor-edit-chapter')) {
            ClassicEditor.create(document.querySelector('#editor-edit-chapter'), config)
                .then(editor => { window.editorEditChapter = editor; })
                .catch(error => { console.error(error); });
        }
        if (document.querySelector('#editor-add-material')) {
            ClassicEditor.create(document.querySelector('#editor-add-material'), config).catch(error => { console.error(error); });
        }
        if (document.querySelector('#editor-edit-material')) {
            ClassicEditor.create(document.querySelector('#editor-edit-material'), config)
                .then(editor => { window.editorEditMaterial = editor; })
                .catch(error => { console.error(error); });
        }
    });
</script>
<style>
    .ck-editor__editable {
        min-height: 150px;
    }
</style>
<?php require_once 'layouts/footer.php'; ?>