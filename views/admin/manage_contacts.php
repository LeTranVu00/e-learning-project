<?php $pageTitle = 'Quản lý Liên hệ'; require_once 'layouts/header.php'; ?>

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quản lý Liên hệ</h1>
                    <p class="text-gray-500 mt-1">Quản lý các tin nhắn và yêu cầu hỗ trợ từ người dùng</p>
                </div>
            </div>

            <!-- BẢNG DANH SÁCH LIÊN HỆ -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <div class="overflow-x-auto"><table class="w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-sm text-gray-500 uppercase">
                                <th class="py-4 px-6 font-semibold">Khách hàng</th>
                                <th class="py-4 px-6 font-semibold">Chủ đề</th>
                                <th class="py-4 px-6 font-semibold">Ngày gửi</th>
                                <th class="py-4 px-6 font-semibold">Trạng thái</th>
                                <th class="py-4 px-6 font-semibold text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" x-data="{ showMessageModal: false, activeMessage: {} }">
                            <?php if (empty($contacts)): ?>
                                <tr><td colspan="5" class="py-8 text-center text-gray-500">Chưa có liên hệ nào!</td></tr>
                            <?php else: ?>
                                <?php foreach($contacts as $contact): ?>
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-gray-900"><?= htmlspecialchars($contact['name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($contact['email']) ?></div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-gray-700 font-medium line-clamp-1 max-w-[200px]"><?= htmlspecialchars($contact['subject']) ?></div>
                                        </td>
                                        <td class="py-4 px-6 text-gray-500 text-sm font-medium">
                                            <?= date('H:i - d/m/Y', strtotime($contact['created_at'])) ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <?php if($contact['status'] === 'resolved'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                    Đã xử lý
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                                    Chưa xử lý
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="activeMessage = <?= htmlspecialchars(json_encode($contact)) ?>; showMessageModal = true" class="text-blue-600 bg-blue-50 hover:bg-blue-500 hover:text-white p-2 rounded-lg transition shadow-sm border border-blue-100" title="Đọc tin nhắn">
                                                    <i class="fa-solid fa-envelope-open"></i>
                                                </button>
                                                <?php if($contact['status'] === 'pending'): ?>
                                                <a href="?action=admin_resolve_contact&id=<?= $contact['id'] ?>" class="text-green-600 bg-green-50 hover:bg-green-500 hover:text-white p-2 rounded-lg transition shadow-sm border border-green-100" title="Đánh dấu đã phản hồi">
                                                    <i class="fa-solid fa-check"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <!-- Modal: Đọc tin nhắn -->
                            <div x-show="showMessageModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                                <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm" @click="showMessageModal = false"></div>
                                <div class="flex items-center justify-center min-h-screen px-4 py-8">
                                    <div x-show="showMessageModal" class="relative bg-white rounded-3xl text-left shadow-2xl w-full max-w-lg z-50 flex flex-col overflow-hidden border border-gray-100">
                                        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center shrink-0">
                                            <h3 class="text-xl font-bold">Nội dung tin nhắn</h3>
                                            <button @click="showMessageModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                                        </div>
                                        <div class="px-6 py-6 space-y-4">
                                            <div>
                                                <span class="text-sm text-gray-500">Từ:</span>
                                                <div class="font-bold text-gray-900" x-text="activeMessage.name"></div>
                                                <div class="text-sm text-blue-600" x-text="activeMessage.email"></div>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-500">Chủ đề:</span>
                                                <div class="font-bold text-gray-800" x-text="activeMessage.subject"></div>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-500">Nội dung:</span>
                                                <div class="bg-gray-50 p-4 rounded-xl border mt-1 text-gray-700 whitespace-pre-wrap" x-text="activeMessage.message"></div>
                                            </div>
                                        </div>
                                        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 shrink-0">
                                            <button @click="showMessageModal = false" class="px-5 py-2 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition font-medium">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tbody>
                    </table></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <?php if (isset($_SESSION['success'])): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-10 right-10 z-50 flex items-center gap-4 bg-white px-6 py-4 rounded-2xl shadow-2xl border border-green-100">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-500"><i class="fa-solid fa-check text-xl"></i></div>
            <div>
                <h4 class="font-bold text-gray-900">Thành công!</h4>
                <p class="text-sm text-gray-500"><?= $_SESSION['success'] ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
<?php require_once 'layouts/footer.php'; ?>
