<?php $pageTitle = 'Thêm khóa học mới'; require_once 'layouts/header.php'; ?>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="?action=admin_dashboard" class="text-gray-500 hover:text-primary transition font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Tạo khóa học mới 🚀</h1>

            <form action="?action=admin_store_course" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tên khóa học <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required placeholder="VD: Lập trình PHP & MySQL từ con số 0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Danh mục khóa học</label>
                        <select name="category_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition bg-white">
                            <option value="">-- Chọn danh mục --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Giảng viên</label>
                        <input type="text" name="instructor" placeholder="VD: Nguyễn Văn A" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Giá khóa học (VNĐ)</label>
                        <input type="number" name="price" min="0" value="0" step="1000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Giá gốc / Giá niêm yết (VNĐ) <span class="text-xs text-gray-400">(để gạch ngang)</span></label>
                        <input type="number" name="original_price" min="0" value="0" step="1000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cấp độ</label>
                        <select name="level" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none transition bg-white">
                            <option value="Sơ cấp">Sơ cấp</option>
                            <option value="Trung cấp">Trung cấp</option>
                            <option value="Nâng cao">Nâng cao</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Số giờ học</label>
                        <input type="number" name="duration_hours" min="0" value="0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Số bài giảng</label>
                        <input type="number" name="total_lessons" min="0" value="0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ngôn ngữ giảng dạy</label>
                        <input type="text" name="language" value="Tiếng Việt" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh bìa (Thumbnail)</label>
                        <input type="file" name="thumbnail" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary outline-none transition bg-white">
                    </div>
                </div>

                <!-- ====== Thông tin Card Sidebar ====== -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                    <h3 class="text-sm font-bold text-blue-700 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-sidebar"></i> Thông tin hiển thị trên Card khóa học
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fa-solid fa-calendar-day text-blue-400 mr-1"></i> Ngày khai giảng
                            </label>
                            <input type="date" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fa-solid fa-clock text-blue-400 mr-1"></i> Giờ học
                                <span class="text-gray-400 font-normal">(VD: 19h - 21h)</span>
                            </label>
                            <input type="text" name="study_time" placeholder="VD: 19h - 21h" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fa-solid fa-calendar-week text-blue-400 mr-1"></i> Lịch học
                                <span class="text-gray-400 font-normal">(cách nhau bằng dấu phẩy)</span>
                            </label>
                            <input type="text" name="schedule" placeholder="VD: Thứ 2, Thứ 4, Thứ 5, Thứ 7" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fa-brands fa-square-phone text-blue-400 mr-1"></i> Số điện thoại / Zalo tư vấn
                            </label>
                            <input type="text" name="contact_phone" placeholder="VD: 0965303260" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 outline-none transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bạn sẽ học được gì? (Benefits)</label>
                    <textarea name="benefits" id="course_benefits"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Yêu cầu khóa học (Requirements)</label>
                    <textarea name="requirements" id="course_requirements"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nội dung chi tiết khóa học</label>
                    <textarea name="description" id="course_description"></textarea>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-primary hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu khóa học
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        const editorConfig = {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
        };
        
        ClassicEditor.create(document.querySelector('#course_description'), editorConfig).catch(error => console.error(error));
        ClassicEditor.create(document.querySelector('#course_benefits'), editorConfig).catch(error => console.error(error));
        ClassicEditor.create(document.querySelector('#course_requirements'), editorConfig).catch(error => console.error(error));
    </script>
<?php require_once 'layouts/footer.php'; ?>