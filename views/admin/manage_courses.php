<?php $pageTitle = 'Quản lý Khóa học';
require_once 'layouts/header.php'; ?>
<div class="flex-1 p-4 sm:p-6 md:p-8" x-data="{ 
    showAddModal: false, 
          showEditModal: false,
          showDetailModal: false,
          detailData: {},
          editData: {},
          openAdd() {
              this.showAddModal = true;
              this.$nextTick(() => {
                  const config = { toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ] };
                  if (!window.editor) {
                      ClassicEditor.create(document.querySelector('#editor_add'), config)
                          .then(editor => { window.editor = editor; })
                          .catch(err => console.error(err));
                  }
                  if (!window.editorBenefits) {
                      ClassicEditor.create(document.querySelector('#editor_add_benefits'), config)
                          .then(editor => { window.editorBenefits = editor; })
                          .catch(err => console.error(err));
                  }
                  if (!window.editorReqs) {
                      ClassicEditor.create(document.querySelector('#editor_add_reqs'), config)
                          .then(editor => { window.editorReqs = editor; })
                          .catch(err => console.error(err));
                  }
              });
          },
          openEdit(course) {
              this.editData = { ...course };
              this.editData.price          = course.price          || 0;
              let discount = 0;
              if (course.original_price > course.price && course.price > 0) {
                  discount = Math.round((1 - course.price / course.original_price) * 100);
              } else if (course.original_price > 0 && course.price == 0) {
                  discount = 100;
              }
              this.editData.discount_percent = discount;
              this.editData.duration_hours = course.duration_hours || 0;
              this.editData.total_lessons  = course.total_lessons  || 0;
              this.showEditModal = true;
              this.$nextTick(() => {
                  // Nếu editor chưa được khởi tạo, khởi tạo ngay
                  const config = { toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ] };
                  if (!window.editorEdit) {
                      ClassicEditor.create(document.querySelector('#editor_edit'), config)
                          .then(editor => { window.editorEdit = editor; editor.setData(course.description || ''); })
                          .catch(err => console.error(err));
                  } else {
                      window.editorEdit.setData(course.description || '');
                  }
                  if (!window.editorEditBenefits) {
                      ClassicEditor.create(document.querySelector('#editor_edit_benefits'), config)
                          .then(editor => { window.editorEditBenefits = editor; editor.setData(course.benefits || ''); })
                          .catch(err => console.error(err));
                  } else {
                      window.editorEditBenefits.setData(course.benefits || '');
                  }
                  if (!window.editorEditReqs) {
                      ClassicEditor.create(document.querySelector('#editor_edit_reqs'), config)
                          .then(editor => { window.editorEditReqs = editor; editor.setData(course.requirements || ''); })
                          .catch(err => console.error(err));
                  } else {
                      window.editorEditReqs.setData(course.requirements || '');
                  }
              });
          },
          async submitAddCourse(event) {
              const form = event.target;
              const formData = new FormData(form);
              
              if(window.editor) formData.set('description', window.editor.getData());
              if(window.editorBenefits) formData.set('benefits', window.editorBenefits.getData());
              if(window.editorReqs) formData.set('requirements', window.editorReqs.getData());

              if (!formData.get('title') || formData.get('title').trim() === '') {
                  showToast('Vui lòng nhập tên khóa học!', 'error');
                  return;
              }
              const thumbnailFile = formData.get('thumbnail');
              if (!thumbnailFile || thumbnailFile.size === 0) {
                  showToast('Vui lòng chọn ảnh bìa!', 'error');
                  return;
              }

              const btn = document.querySelector('button[form=' + form.id + ']') || form.querySelector('button[type=submit]');
              const oldText = btn ? btn.innerHTML : '';
              if (btn) {
                  btn.innerHTML = 'Đang xử lý...';
                  btn.disabled = true;
              }

              try {
                  const csrfToken = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                  const response = await fetch('?action=admin_store_course', {
                      method: 'POST',
                      body: formData,
                      headers: { 
                          'X-Requested-With': 'XMLHttpRequest',
                          'X-CSRF-TOKEN': csrfToken 
                      }
                  });
                  const data = await response.json();
                  if (data.success) {
                      showToast(data.message, 'success');
                      this.showAddModal = false;
                      form.reset();
                      if(window.editor) window.editor.setData('');
                      if(window.editorBenefits) window.editorBenefits.setData('');
                      if(window.editorReqs) window.editorReqs.setData('');
                      
                      // Fetch HTML mới của bảng khóa học để cập nhật mà không load lại trang
                      const htmlResp = await fetch(window.location.href);
                      const htmlText = await htmlResp.text();
                      const parser = new DOMParser();
                      const doc = parser.parseFromString(htmlText, 'text/html');
                      const newTableBody = doc.querySelector('#coursesTableBody');
                      if (newTableBody) {
                          document.querySelector('#coursesTableBody').innerHTML = newTableBody.innerHTML;
                      }
                  } else {
                      showToast(data.message, 'error');
                  }
              } catch(e) {
                  showToast('Lỗi kết nối tới máy chủ!', 'error');
              } finally {
                  if (btn) {
                      btn.innerHTML = oldText;
                      btn.disabled = false;
                  }
              }
          },
          async deleteCourse(id) {
              if (!confirm('Bạn có chắc chắn muốn XÓA khóa học này không? Mọi bài giảng bên trong sẽ bị mất!')) return;
              try {
                  const response = await fetch('?action=admin_delete_course&id=' + id, {
                      headers: { 'X-Requested-With': 'XMLHttpRequest' }
                  });
                  const data = await response.json();
                  if (data.success) {
                      showToast(data.message, 'success');
                      // Tự động load lại bảng mà không giật trang
                      const htmlResp = await fetch(window.location.href);
                      const htmlText = await htmlResp.text();
                      const parser = new DOMParser();
                      const doc = parser.parseFromString(htmlText, 'text/html');
                      const newTableBody = doc.querySelector('#coursesTableBody');
                      if (newTableBody) {
                          document.querySelector('#coursesTableBody').innerHTML = newTableBody.innerHTML;
                      }
                  } else {
                      showToast(data.message, 'error');
                  }
              } catch(e) {
                  showToast('Lỗi kết nối tới máy chủ!', 'error');
              }
          }
      }">

    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quản lý Khóa học</h1>
        </div>
        <button @click="openAdd()"
            class="bg-dark hover:bg-gray-800 text-white font-medium py-2.5 px-6 rounded-xl transition shadow-md flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Thêm Khóa Học
        </button>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="index.php" class="flex flex-wrap gap-4 items-end">
            <input type="hidden" name="action" value="admin_manage_courses">

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tìm
                    kiếm</label>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="Tên khóa học, giảng viên..."
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Ngày
                    tạo</label>
                <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>"
                    class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm text-gray-700">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sắp
                    xếp</label>
                <select name="sort"
                    class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition text-sm font-medium text-gray-700">
                    <option value="latest" <?= ($_GET['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                    <option value="price_high" <?= ($_GET['sort'] ?? '') === 'price_high' ? 'selected' : '' ?>>Giá cao nhất
                    </option>
                    <option value="price_low" <?= ($_GET['sort'] ?? '') === 'price_low' ? 'selected' : '' ?>>Giá thấp nhất
                    </option>
                </select>
            </div>

            <button type="submit"
                class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-xl transition text-sm">
                <i class="fa-solid fa-filter mr-2"></i> Lọc
            </button>

            <?php if (!empty($_GET['search']) || !empty($_GET['date']) || (!empty($_GET['sort']) && $_GET['sort'] !== 'latest')): ?>
                <a href="?action=admin_manage_courses"
                    class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition text-sm">
                    Xóa lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div
        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="bg-gray-50 border-b border-gray-100 text-sm text-gray-500 uppercase tracking-wider">
                    <th class="p-4 font-semibold">Hình ảnh</th>
                    <th class="p-4 font-semibold">Tên khóa học</th>
                    <th class="p-4 font-semibold">Danh mục</th>
                    <th class="p-4 font-semibold">Giảng viên</th>
                    <th class="p-4 font-semibold">Giá</th>
                    <th class="p-4 font-semibold text-center w-48">Thao tác</th>
                </tr>
            </thead>
            <tbody id="coursesTableBody" class="divide-y divide-gray-100">
                <?php if (!empty($courses)):
                    foreach ($courses as $course): ?>
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-4">
                                <?php
                                $thumb = $course['thumbnail'] ?? '';
                                $thumb_display = !empty($thumb)
                                    ? (str_starts_with($thumb, 'http') ? $thumb : '/e-learning-project/public/' . $thumb)
                                    : 'https://placehold.co/100x70/4f46e5/fff?text=No+Image';
                                ?>
                                <img src="<?= htmlspecialchars($thumb_display) ?>"
                                    class="w-24 h-16 object-cover rounded-lg border border-gray-200">
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-gray-800"><?= htmlspecialchars($course['title']) ?></p>
                                <?php if (!empty($course['start_date'])): ?>
                                    <p class="text-xs text-gray-400 mt-1"><i
                                            class="fa-solid fa-calendar mr-1"></i><?= date('d/m/Y', strtotime($course['start_date'])) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                <span
                                    class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-semibold"><?= htmlspecialchars($course['category_name'] ?? 'Chưa phân loại') ?></span>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                <?= htmlspecialchars($course['instructor'] ?? '—') ?></td>
                            <td class="p-4 text-sm">
                                <div class="flex flex-col">
                                    <span
                                        class="font-semibold <?= ($course['price'] ?? 0) > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                        <?= ($course['price'] ?? 0) > 0 ? number_format($course['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                                    </span>
                                    <?php if (!empty($course['original_price']) && $course['original_price'] > ($course['price'] ?? 0)): ?>
                                        <del
                                            class="text-xs text-gray-400 mt-0.5"><?= number_format($course['original_price'], 0, ',', '.') ?>đ</del>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button
                                        @click="detailData = <?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>; showDetailModal = true"
                                        title="Xem chi tiết"
                                        class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition shadow-sm border border-gray-200">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <!-- Nút đánh dấu Nổi bật -->
                                    <a href="?action=admin_toggle_course_featured&id=<?= $course['id'] ?><?= isset($_GET['page']) ? '&page=' . $_GET['page'] : '' ?>"
                                        title="<?= !empty($course['is_featured']) ? 'Hủy nổi bật' : 'Đánh dấu nổi bật' ?>"
                                        class="w-10 h-10 rounded-full flex items-center justify-center transition shadow-sm border <?= !empty($course['is_featured']) ? 'bg-orange-50 text-orange-500 border-orange-200 hover:bg-orange-100' : 'bg-gray-50 text-gray-400 border-gray-200 hover:bg-gray-200' ?>">
                                        <i class="fa-solid fa-star"></i>
                                    </a>

                                    <a href="?action=admin_manage_content&id=<?= $course['id'] ?>" title="Nội dung bài giảng"
                                        class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition shadow-sm border border-blue-100">
                                        <i class="fa-solid fa-list-check"></i>
                                    </a>
                                    <button
                                        @click="openEdit(<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>)"
                                        title="Sửa khóa học"
                                        class="w-10 h-10 rounded-full flex items-center justify-center bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition shadow-sm border border-yellow-100">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click.prevent="deleteCourse(<?= $course['id'] ?>)" title="Xóa khóa học"
                                        class="w-10 h-10 rounded-full flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition shadow-sm border border-red-100">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">Chưa có khóa học nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <?php
        $filterParams = '';
        if (!empty($_GET['search']))
            $filterParams .= '&search=' . urlencode($_GET['search']);
        if (!empty($_GET['date']))
            $filterParams .= '&date=' . urlencode($_GET['date']);
        if (!empty($_GET['sort']))
            $filterParams .= '&sort=' . urlencode($_GET['sort']);
        ?>
        <div class="mt-6 flex justify-center">
            <nav class="flex items-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="?action=admin_manage_courses&page=<?= $page - 1 ?><?= $filterParams ?>"
                        class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?action=admin_manage_courses&page=<?= $i ?><?= $filterParams ?>"
                        class="px-4 py-2 <?= $i === $page ? 'bg-primary text-white border-primary' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' ?> border rounded-lg font-medium transition">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?action=admin_manage_courses&page=<?= $page + 1 ?><?= $filterParams ?>"
                        class="px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>


    <!-- =============================================
         MODAL: THÊM KHÓA HỌC MỚI
    ============================================= -->
    <div x-cloak x-show="showAddModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="modal-overlay">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div class="modal-card relative bg-white rounded-3xl overflow-hidden text-left shadow-2xl w-full max-w-4xl z-50 flex flex-col border border-gray-100"
            style="max-height: min(92vh, 900px);" @click.stop>
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                <h3 class="text-xl font-bold flex items-center gap-2"><i class="fa-solid fa-plus text-primary"></i> Thêm
                    khóa học mới</h3>
                <button type="button" @click="showAddModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition"><i
                        class="fa-solid fa-xmark text-xl pointer-events-none"></i></button>
            </div>
            <div class="px-6 py-6 overflow-y-auto grow" style="overscroll-behavior: contain;">
                <form @submit.prevent="submitAddCourse" id="formAdd" class="space-y-5" enctype="multipart/form-data"
                    novalidate>

                    <!-- Row 1: Tên + Danh mục -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Tên khóa
                                học <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Danh mục
                                khóa học</label>
                            <select name="category_id"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm text-gray-700">
                                <option value="">-- Chọn danh mục --</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Giảng viên đã được tự động lấy theo session user_name -->

                    <!-- Row 2: Giá + Giá gốc -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Giá bán
                                (VNĐ)</label>
                            <input type="number" name="price" min="0" value="0" step="1000"
                                class="w-full pl-5 pr-2 py-3.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">% Giảm giá <span class="text-xs text-gray-400">(Tùy chọn)</span></label>
                            <input type="number" name="discount_percent" min="0" max="100" value="0"
                                class="w-full pl-5 pr-2 py-3.5 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm">
                        </div>
                    </div>

                    <!-- Row 3: Cấp độ + Số giờ học -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Cấp
                                độ</label>
                            <select name="level"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm text-gray-700">
                                <option>Sơ cấp</option>
                                <option>Trung cấp</option>
                                <option>Nâng cao</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Số giờ
                                học</label>
                            <input type="number" name="duration_hours" min="0" value="0"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm">
                        </div>
                    </div>

                    <!-- Row 4: Ngôn ngữ + Ảnh bìa -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Ngôn
                                ngữ</label>
                            <input type="text" name="language" value="Tiếng Việt"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Ảnh bìa
                                <span class="text-red-500">*</span></label>
                            <input type="file" name="thumbnail" accept="image/*" required
                                class="w-full px-5 py-2.5 border border-gray-200 rounded-2xl outline-none bg-white shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>

                    <!-- Khu vực Card sidebar -->
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                        <h4 class="text-sm font-bold text-blue-700 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-id-card"></i> Thông tin hiển thị trên Card khóa học
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-calendar-day text-blue-400 mr-1"></i> Ngày khai giảng
                                </label>
                                <input type="date" name="start_date"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-clock text-blue-400 mr-1"></i> Giờ học <span
                                        class="text-gray-400 font-normal">(VD: 19h - 21h)</span>
                                </label>
                                <input type="text" name="study_time" placeholder="VD: 19h - 21h"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-calendar-week text-blue-400 mr-1"></i> Lịch học <span
                                        class="text-gray-400 font-normal">(cách nhau dấu phẩy)</span>
                                </label>
                                <input type="text" name="schedule" placeholder="VD: Thứ 2, Thứ 4, Thứ 5, Thứ 7"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-phone text-blue-400 mr-1"></i> Số Zalo tư vấn
                                </label>
                                <input type="text" name="contact_phone" placeholder="VD: 0965303260"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>

                    <!-- Rich text: Mô tả -->
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Mô tả khóa học</label>
                        <textarea name="description" id="editor_add"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Bạn sẽ học được gì?</label>
                        <textarea name="benefits" id="editor_add_benefits"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Yêu cầu khóa học</label>
                        <textarea name="requirements" id="editor_add_reqs"></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                <button @click="showAddModal = false"
                    class="px-6 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 transition font-medium">Hủy</button>
                <button type="submit" form="formAdd"
                    class="bg-dark text-white font-bold py-2.5 px-6 rounded-xl hover:bg-gray-800 transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Lưu khóa học
                </button>
            </div>
        </div>
    </div>

    <!-- =============================================
         MODAL: SỬa KHÓA HỌC
    ============================================= -->
    <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="modal-overlay">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showEditModal = false"></div>
        <div class="modal-card relative bg-white rounded-3xl overflow-hidden text-left shadow-2xl w-full max-w-4xl z-50 flex flex-col border border-gray-100"
            style="max-height: min(92vh, 900px);" @click.stop>
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                <h3 class="text-xl font-bold flex items-center gap-2"><i
                        class="fa-solid fa-pen-to-square text-yellow-500"></i> Sửa khóa học</h3>
                <button type="button" @click="showEditModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition"><i
                        class="fa-solid fa-xmark text-xl pointer-events-none"></i></button>
            </div>
            <div class="px-6 py-6 overflow-y-auto grow" style="overscroll-behavior: contain;">
                <form action="?action=admin_update_course" method="POST" id="formEdit" class="space-y-5"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id" :value="editData.id">
                    <input type="hidden" name="old_thumbnail" :value="editData.thumbnail">

                    <!-- Row 1: Tên + Danh mục -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Tên khóa
                                học <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editData.title" required
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Danh mục
                                khóa học</label>
                            <select name="category_id" x-model="editData.category_id"
                                class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl outline-none focus:ring-2 focus:ring-primary focus:bg-white transition shadow-sm text-gray-700">
                                <option value="">-- Chọn danh mục --</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Giảng viên đã được tự động lấy theo session user_name -->

                    <!-- Row 2: Giá + Giá gốc -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Giá bán (VNĐ)</label>
                            <input type="number" name="price" x-model="editData.price" min="0" step="1000"
                                class="w-full pl-4 pr-2 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Số % Giảm giá <span
                                    class="text-xs text-gray-400">(Tùy chọn)</span></label>
                            <input type="number" name="discount_percent" x-model="editData.discount_percent" min="0" max="100"
                                class="w-full pl-4 pr-2 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <!-- Row 3: Cấp độ + Số giờ học (Số bài giảng tự đếm từ chương trình học) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Cấp độ</label>
                            <select name="level" x-model="editData.level"
                                class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary bg-white">
                                <option>Sơ cấp</option>
                                <option>Trung cấp</option>
                                <option>Nâng cao</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Số giờ học</label>
                            <input type="number" name="duration_hours" x-model="editData.duration_hours" min="0"
                                class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <!-- Row 4: Ngôn ngữ + Ảnh bìa mới -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Ngôn ngữ</label>
                            <input type="text" name="language" x-model="editData.language"
                                class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5">Ảnh bìa mới <span
                                    class="text-xs text-gray-400">(bỏ trống nếu không đổi)</span></label>
                            <input type="file" name="thumbnail" accept="image/*"
                                class="w-full px-4 py-2.5 border rounded-xl outline-none bg-white">
                        </div>
                    </div>

                    <!-- Khu vực Card sidebar -->
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                        <h4 class="text-sm font-bold text-blue-700 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-id-card"></i> Thông tin hiển thị trên Card khóa học
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-calendar-day text-blue-400 mr-1"></i> Ngày khai giảng
                                </label>
                                <input type="date" name="start_date" x-model="editData.start_date"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-clock text-blue-400 mr-1"></i> Giờ học <span
                                        class="text-gray-400 font-normal">(VD: 19h - 21h)</span>
                                </label>
                                <input type="text" name="study_time" x-model="editData.study_time"
                                    placeholder="VD: 19h - 21h"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-calendar-week text-blue-400 mr-1"></i> Lịch học <span
                                        class="text-gray-400 font-normal">(cách nhau dấu phẩy)</span>
                                </label>
                                <input type="text" name="schedule" x-model="editData.schedule"
                                    placeholder="VD: Thứ 2, Thứ 4, Thứ 5, Thứ 7"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1.5 text-gray-700">
                                    <i class="fa-solid fa-phone text-blue-400 mr-1"></i> Số Zalo tư vấn
                                </label>
                                <input type="text" name="contact_phone" x-model="editData.contact_phone"
                                    placeholder="VD: 0965303260"
                                    class="w-full px-4 py-3 border border-gray-300 bg-white rounded-xl outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                    </div>

                    <!-- Rich text editors -->
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Mô tả khóa học</label>
                        <textarea name="description" id="editor_edit"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Bạn sẽ học
                            được gì?</label>
                        <textarea name="benefits" id="editor_edit_benefits"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Yêu cầu khóa
                            học</label>
                        <textarea name="requirements" id="editor_edit_reqs"></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end gap-3 shrink-0">
                <button @click="showEditModal = false"
                    class="px-6 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 transition font-medium">Hủy</button>
                <button type="submit" form="formEdit"
                    class="bg-primary text-white font-bold py-2.5 px-6 rounded-xl hover:bg-yellow-600 transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i>Lưu thay đổi
                </button>
            </div>
        </div>
    </div>

    <!-- =============================================
         MODAL: XEM CHI TIẾT KHÓA HỌC
    ============================================= -->
    <div x-cloak x-show="showDetailModal" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="modal-overlay">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showDetailModal = false"></div>
        <div class="modal-card relative bg-white rounded-3xl overflow-hidden text-left shadow-2xl w-full max-w-lg z-50 flex flex-col border border-gray-100"
            style="max-height: min(92vh, 700px);" @click.stop>
            <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                <h3 class="text-xl font-bold flex items-center gap-2"><i
                        class="fa-solid fa-circle-info text-blue-500"></i> Chi tiết khóa học</h3>
                <button type="button" @click="showDetailModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition"><i
                        class="fa-solid fa-xmark text-xl pointer-events-none"></i></button>
            </div>
            <div class="px-6 py-6 overflow-y-auto grow space-y-4" style="overscroll-behavior: contain;">
                <div class="flex gap-4">
                    <img :src="detailData.thumbnail && detailData.thumbnail.startsWith('http') ? detailData.thumbnail : (detailData.thumbnail ? '/e-learning-project/public/' + detailData.thumbnail : 'https://placehold.co/100x70/4f46e5/fff?text=No+Image')"
                        class="w-32 h-24 object-cover rounded-xl border">
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-gray-800 leading-tight"
                            x-text="detailData.title"></h4>
                        <p class="text-primary font-bold mt-2"
                            x-text="(detailData.price > 0 ? new Intl.NumberFormat('vi-VN').format(detailData.price) + 'đ' : 'Miễn phí')">
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border space-y-3">
                    <div class="flex items-center justify-between border-b pb-2">
                        <span class="text-gray-500 font-medium">Người đăng (Giảng viên):</span>
                        <span class="font-bold text-gray-800"
                            x-text="detailData.instructor || '—'"></span>
                    </div>
                    <div class="flex items-center justify-between border-b pb-2">
                        <span class="text-gray-500 font-medium">Thời lượng học:</span>
                        <span class="font-semibold text-gray-700"
                            x-text="(detailData.duration_hours || 0) + ' giờ'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 font-medium">Số bài giảng:</span>
                        <span class="font-semibold text-gray-700"
                            x-text="(detailData.total_lessons || 0) + ' bài'"></span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end shrink-0">
                <button @click="showDetailModal = false"
                    class="px-6 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 transition font-medium">Đóng</button>
            </div>
        </div>
    </div>


</div>
<?php require_once 'layouts/footer.php'; ?>