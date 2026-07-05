<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8" data-aos="fade-up">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Giỏ hàng của bạn</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Xem lại các khóa học bạn đã chọn trước khi thanh toán.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <?php if (empty($cartItems)): ?>
                        <div class="p-12 text-center">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-cart-arrow-down text-4xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Giỏ hàng trống</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-6">Bạn chưa chọn khóa học nào. Hãy khám phá các khóa học của chúng tôi nhé!</p>
                            <a href="?action=courses" class="inline-flex items-center gap-2 bg-primary hover:bg-yellow-600 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="fa-solid fa-compass"></i> Khám phá ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700" id="cart-item-list">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="p-6 flex flex-col sm:flex-row gap-6 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-700" id="cart-item-<?= $item['id'] ?>">
                                    <div class="shrink-0">
                                        <?php $thumbnail = !empty($item['thumbnail']) ? htmlspecialchars($item['thumbnail']) : 'https://placehold.co/300x200/f59e0b/white?text=Course'; ?>
                                        <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="w-full sm:w-40 h-28 object-cover rounded-xl shadow-sm">
                                    </div>
                                    <div class="flex-grow flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 leading-tight">
                                                <a href="?action=detail&id=<?= $item['id'] ?>" class="hover:text-primary transition-colors"><?= htmlspecialchars($item['title']) ?></a>
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Giảng viên: <?= htmlspecialchars($item['instructor'] ?? 'E-Learning') ?></p>
                                        </div>
                                        <div class="flex items-end justify-between mt-4 sm:mt-0">
                                            <div class="text-lg font-bold <?= (isset($item['price']) && $item['price'] > 0) ? 'text-primary' : 'text-green-500' ?>">
                                                <?= (isset($item['price']) && $item['price'] > 0) ? number_format($item['price'], 0, ',', '.') . 'đ' : 'Miễn phí' ?>
                                            </div>
                                            <button onclick="removeFromCart(<?= $item['id'] ?>)" class="text-gray-400 hover:text-red-500 transition-colors flex items-center gap-1 text-sm font-medium">
                                                <i class="fa-regular fa-trash-can"></i> Xóa
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sticky top-24" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">Tóm tắt đơn hàng</h3>
                    
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600 dark:text-gray-400">Tạm tính:</span>
                        <span class="font-medium text-gray-900 dark:text-white" id="cart-subtotal"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <div class="flex justify-between mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Giảm giá:</span>
                        <span class="font-medium text-green-500">0đ</span>
                    </div>
                    
                    <div class="flex justify-between mb-8">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Tổng cộng:</span>
                        <span class="text-2xl font-bold text-primary" id="cart-total"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                    </div>

                    <a href="?action=checkout_cart" class="w-full bg-primary hover:bg-yellow-600 text-white font-bold py-4 px-4 rounded-xl flex justify-center items-center gap-2 transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1 <?= empty($cartItems) ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' ?>">
                        <span>Thanh toán ngay</span> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    
                    <p class="text-xs text-center text-gray-500 mt-4"><i class="fa-solid fa-shield-halved mr-1"></i> Thanh toán an toàn qua VNPAY</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function removeFromCart(courseId) {
    Swal.fire({
        title: 'Xóa khóa học?',
        text: "Bạn có chắc muốn bỏ khóa học này khỏi giỏ hàng?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('?action=remove_from_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ course_id: courseId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    const badgeDesktop = document.getElementById('cart-badge-desktop');
                    const badgeMobile = document.getElementById('cart-badge-mobile');
                    
                    if (data.cart_count > 0) {
                        badgeDesktop.textContent = data.cart_count;
                        badgeMobile.textContent = data.cart_count;
                        badgeDesktop.classList.remove('hidden');
                        badgeMobile.classList.remove('hidden');
                    } else {
                        badgeDesktop.classList.add('hidden');
                        badgeMobile.classList.add('hidden');
                    }
                    
                    // Update totals
                    document.getElementById('cart-subtotal').textContent = data.total_price;
                    document.getElementById('cart-total').textContent = data.total_price;
                    
                    // Remove item smoothly
                    const itemEl = document.getElementById('cart-item-' + courseId);
                    itemEl.style.opacity = '0';
                    itemEl.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        itemEl.remove();
                        if (data.cart_count === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    }, 300);

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Lỗi!', 'Không thể kết nối đến server.', 'error');
            });
        }
    })
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
