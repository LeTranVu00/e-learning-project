<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Nội dung - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script> tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } } </script>
</head>
<body class="bg-gray-100 font-sans p-8">

    <div class="max-w-4xl mx-auto" x-data="{ 
        showChapterModal: false, 
        showMaterialModal: false, 
        showEditChapterModal: false,
        showEditMaterialModal: false,
        currentChapterId: null,
        editChapterData: { id: '', title: '' },
        editMaterialData: { id: '', title: '', type: '', file_url: '' },

        openEditChapter(id, title) {
            this.editChapterData.id = id;
            this.editChapterData.title = title;
            this.showEditChapterModal = true;
        },
        openEditMaterial(id, title, type, file_url) {
            this.editMaterialData.id = id;
            this.editMaterialData.title = title;
            this.editMaterialData.type = type;
            this.editMaterialData.file_url = file_url;
            this.showEditMaterialModal = true;
        }
    }">
        
        <div class="mb-6 flex justify-between items-center">
            <a href="?action=admin_manage_courses" class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Quản lý khóa học
            </a>
            <button @click="showChapterModal = true" class="bg-dark hover:bg-gray-800 text-white font-bold py-2.5 px-6 rounded-xl transition shadow-lg flex items-center gap-2">
                <i class="fa-solid fa-folder-plus"></i> Thêm Chương Mới
            </button>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">
            Khóa học: <span class="text-primary"><?= htmlspecialchars($course['title']) ?></span>
        </h1>

        <?php if (empty($curriculum)): ?>
            <div class="text-center bg-white p-10 rounded-2xl shadow-sm border border-gray-100">Khóa học này chưa có chương nào!</div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($curriculum as $chapter): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        
                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-200 gap-3">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fa-solid fa-list mr-2 text-gray-400"></i> <?= htmlspecialchars($chapter['title']) ?>
                            </h2>
                            
                            <div class="flex items-center gap-2">
                                <button @click="currentChapterId = <?= $chapter['id'] ?>; showMaterialModal = true" title="Thêm bài giảng" class="text-sm bg-primary/10 text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg transition font-medium">
                                    <i class="fa-solid fa-plus mr-1"></i> Thêm bài
                                </button>
                                <button @click="openEditChapter(<?= $chapter['id'] ?>, '<?= htmlspecialchars(addslashes($chapter['title'])) ?>')" title="Sửa Chương" class="w-8 h-8 rounded-full flex items-center justify-center bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <a href="?action=admin_delete_chapter&id=<?= $chapter['id'] ?>&course_id=<?= $course['id'] ?>" onclick="return confirm('Xóa chương này sẽ mất toàn bộ bài giảng bên trong. Bạn chắc chứ?');" title="Xóa Chương" class="w-8 h-8 rounded-full flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition">
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
                                        <li class="flex items-center justify-between border border-gray-100 p-3 rounded-xl bg-gray-50 hover:bg-white hover:border-gray-300 transition group">
                                            <span class="font-medium text-gray-700 flex items-center gap-2 truncate pr-4">
                                                <i class="fa-solid fa-file-lines text-blue-500"></i> 
                                                <span class="truncate"><?= htmlspecialchars($material['title']) ?></span>
                                            </span>
                                            
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-[10px] uppercase bg-gray-200 text-gray-600 px-2 py-1 rounded font-bold mr-2"><?= $material['type'] ?></span>
                                                <button @click="openEditMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars(addslashes($material['title'])) ?>', '<?= $material['type'] ?>', '<?= htmlspecialchars(addslashes($material['file_url'])) ?>')" title="Sửa bài" class="w-7 h-7 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 hover:bg-yellow-500 hover:text-white transition opacity-0 group-hover:opacity-100">
                                                    <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                                </button>
                                                <a href="?action=admin_delete_material&id=<?= $material['id'] ?>&course_id=<?= $course['id'] ?>" onclick="return confirm('Xóa bài giảng này?');" title="Xóa bài" class="w-7 h-7 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 hover:bg-red-500 hover:text-white transition opacity-0 group-hover:opacity-100">
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

        <div x-show="showChapterModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl p-6" @click.away="showChapterModal = false">
                <h3 class="text-xl font-bold mb-4">Thêm Chương mới</h3>
                <form action="?action=admin_store_chapter" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Tên Chương</label>
                        <input type="text" name="title" required placeholder="VD: Chương 1 - Nhập môn" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="showChapterModal = false" class="px-4 py-2 bg-gray-200 rounded-xl font-medium">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-dark text-white rounded-xl font-medium">Lưu Chương</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showEditChapterModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl p-6" @click.away="showEditChapterModal = false">
                <h3 class="text-xl font-bold mb-4">Sửa tên Chương</h3>
                <form action="?action=admin_update_chapter" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="id" :value="editChapterData.id">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Tên Chương</label>
                        <input type="text" name="title" x-model="editChapterData.title" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="showEditChapterModal = false" class="px-4 py-2 bg-gray-200 rounded-xl font-medium">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl font-medium">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showMaterialModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl p-6" @click.away="showMaterialModal = false">
                <h3 class="text-xl font-bold mb-4">Thêm Bài giảng</h3>
                <form action="?action=admin_store_material" method="POST" class="space-y-4">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="chapter_id" :value="currentChapterId">
                    
                    <div>
                        <label class="block text-sm font-semibold mb-2">Tên bài giảng</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Loại định dạng</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                            <option value="video">Video (YouTube/Drive)</option>
                            <option value="file">File (PDF/PPT/ZIP)</option>
                            <option value="link">Đường dẫn ngoài</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Đường dẫn (URL)</label>
                        <input type="text" name="file_url" required class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showMaterialModal = false" class="px-4 py-2 bg-gray-200 rounded-xl font-medium">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-dark text-white rounded-xl font-medium">Lưu Bài giảng</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showEditMaterialModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl p-6" @click.away="showEditMaterialModal = false">
                <h3 class="text-xl font-bold mb-4">Sửa Bài giảng</h3>
                <form action="?action=admin_update_material" method="POST" class="space-y-4">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="id" :value="editMaterialData.id">
                    
                    <div>
                        <label class="block text-sm font-semibold mb-2">Tên bài giảng</label>
                        <input type="text" name="title" x-model="editMaterialData.title" required class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Loại định dạng</label>
                        <select name="type" x-model="editMaterialData.type" class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                            <option value="video">Video</option>
                            <option value="file">File</option>
                            <option value="link">Link</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Đường dẫn (URL)</label>
                        <input type="text" name="file_url" x-model="editMaterialData.file_url" required class="w-full px-4 py-2 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="showEditMaterialModal = false" class="px-4 py-2 bg-gray-200 rounded-xl font-medium">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl font-medium">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>
</html>