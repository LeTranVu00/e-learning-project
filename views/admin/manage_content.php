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

    <div class="max-w-4xl mx-auto" x-data="{ showChapterModal: false, showMaterialModal: false, currentChapterId: null }">
        
        <div class="mb-6 flex justify-between items-center">
            <a href="?action=admin_dashboard" class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Về Dashboard
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
                        
                        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-200">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-list mr-2 text-gray-400"></i> <?= htmlspecialchars($chapter['title']) ?></h2>
                            <button @click="currentChapterId = <?= $chapter['id'] ?>; showMaterialModal = true" class="text-sm bg-primary/10 text-primary hover:bg-primary hover:text-white px-3 py-1.5 rounded-lg transition font-medium">
                                <i class="fa-solid fa-plus"></i> Thêm bài giảng
                            </button>
                        </div>

                        <div class="p-6">
                            <?php if (empty($chapter['materials'])): ?>
                                <p class="text-gray-400 text-sm italic">Chưa có bài giảng nào.</p>
                            <?php else: ?>
                                <ul class="space-y-3">
                                    <?php foreach ($chapter['materials'] as $material): ?>
                                        <li class="flex items-center justify-between border border-gray-100 p-3 rounded-xl bg-gray-50 hover:bg-white hover:border-gray-300 transition">
                                            <span class="font-medium text-gray-700">
                                                <i class="fa-solid fa-file-lines text-blue-500 mr-2"></i> <?= htmlspecialchars($material['title']) ?>
                                            </span>
                                            <span class="text-xs uppercase bg-gray-200 text-gray-600 px-2 py-1 rounded font-bold"><?= $material['type'] ?></span>
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
                        <input type="text" name="title" required placeholder="VD: Chương 1 - Nhập môn" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showChapterModal = false" class="px-4 py-2 bg-gray-200 rounded-lg font-medium">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-dark text-white rounded-lg font-medium">Lưu Chương</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showMaterialModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl p-6" @click.away="showMaterialModal = false">
                <h3 class="text-xl font-bold mb-4">Thêm Bài giảng / Tài liệu</h3>
                <form action="?action=admin_store_material" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="chapter_id" :value="currentChapterId">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Tên bài giảng</label>
                        <input type="text" name="title" required placeholder="VD: Bài 1: Cài đặt công cụ" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Loại định dạng</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                            <option value="video">Video (YouTube/Drive)</option>
                            <option value="file">File (PDF/PPT/ZIP)</option>
                            <option value="link">Đường dẫn ngoài</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Đường dẫn (URL)</label>
                        <input type="text" name="file_url" required placeholder="Nhập Link Google Drive hoặc Youtube" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showMaterialModal = false" class="px-4 py-2 bg-gray-200 rounded-lg font-medium">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-medium">Lưu Bài giảng</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>
</html>