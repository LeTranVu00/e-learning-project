<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm khóa học mới - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    
    <style>
        .ck-editor__editable_inline {
            min-height: 300px; /* Chiều cao tối thiểu cho khung soạn thảo */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-8">

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
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Giá khóa học (VNĐ) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" required min="0" value="0" step="1000" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ảnh bìa (Thumbnail)</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary outline-none transition bg-white">
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
</body>
</html>