</main>

<footer class="bg-dark text-gray-300 py-10 border-t-4 border-primary mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div>
                <h5 class="text-primary font-bold text-lg mb-4">VỀ E-LEARNING</h5>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Nền tảng học trực tuyến chất lượng cao, cung cấp các khóa học lập trình từ cơ bản đến nâng cao. Giúp bạn tự tin vững bước vào ngành IT.
                </p>
            </div>
            
            <div>
                <h5 class="text-white font-bold text-lg mb-4">LIÊN KẾT NHANH</h5>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="?action=courses" class="hover:text-primary transition duration-300">Tất cả khóa học</a></li>
                    <li><a href="?action=home#contact" class="hover:text-primary transition duration-300">Hướng dẫn thanh toán</a></li>
                    <li><a href="?action=home#contact" class="hover:text-primary transition duration-300">Điều khoản & Bảo mật</a></li>
                </ul>
            </div>
            
            <div>
                <h5 class="text-white font-bold text-lg mb-4">THÔNG TIN LIÊN HỆ</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-center"><i class="fa-solid fa-envelope text-primary w-6 text-center mr-2"></i> support@elearning.vn</li>
                    <li class="flex items-center"><i class="fa-solid fa-phone text-primary w-6 text-center mr-2"></i> 0123 456 789</li>
                    <li class="flex items-center"><i class="fa-solid fa-location-dot text-primary w-6 text-center mr-2"></i> Ký túc xá Đại học, Việt Nam</li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500">
            &copy; 2026 E-Learning Project. Built with PHP & Tailwind CSS.
        </div>
    </div>
</footer>

<!-- ==================== SCROLL TO TOP BUTTON & LANDMARKS ==================== -->
<div x-data="{ 
        show: false, 
        menuOpen: false, 
        landmarks: [] 
     }" 
     x-init="
        document.querySelectorAll('section[id]').forEach(sec => {
            if (sec.id === 'stats') return;
            let h2 = sec.querySelector('h2');
            if (sec.id === 'hero') {
                landmarks.push({ id: sec.id, title: 'Trang chủ' });
            } else if (h2) {
                landmarks.push({ id: sec.id, title: h2.innerText });
            }
        });
     "
     @scroll.window="show = (window.pageYOffset > 500) ? true : false"
     class="fixed bottom-8 right-8 z-[90]"
     @mouseenter="menuOpen = true"
     @mouseleave="menuOpen = false">
     
    <!-- Menu Popup -->
    <div x-show="menuOpen && show && landmarks.length > 0" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         x-cloak
         class="absolute bottom-16 right-0 mb-3 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col py-2 max-h-[60vh] overflow-y-auto hide-scrollbar">
        
        <p class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 mb-1">Đi tới mục</p>
        
        <template x-for="item in landmarks" :key="item.id">
            <a :href="'#' + item.id" 
               @click="menuOpen = false"
               class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-primary/10 hover:text-primary transition-colors border-l-2 border-transparent hover:border-primary block truncate">
                <span x-text="item.title"></span>
            </a>
        </template>
    </div>

    <!-- Nút Cuộn -->
    <button x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-10"
            x-cloak
            @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="bg-gradient-to-r from-primary to-yellow-500 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 focus:outline-none"
            title="Cuộn lên đầu trang">
        <i class="fa-solid fa-arrow-up text-lg"></i>
    </button>
</div>


<!-- =====================================================
     GLOBAL TOAST NOTIFICATION SYSTEM
     Thay thế SweetAlert2 — Toast góc phải màn hình
     Dùng được ở mọi trang qua showToast(message, type)
     ===================================================== -->
<div id="toast-container" 
     class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"
     style="min-width: 300px; max-width: 380px;"></div>

<style>
@keyframes toastSlideIn {
    from { opacity: 0; transform: translateX(110%); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes toastSlideOut {
    from { opacity: 1; transform: translateX(0); }
    to   { opacity: 0; transform: translateX(110%); }
}
.toast-item {
    animation: toastSlideIn 0.35s cubic-bezier(0.21, 1.02, 0.73, 1) forwards;
    pointer-events: all;
}
.toast-item.hide {
    animation: toastSlideOut 0.4s ease forwards;
}
</style>

<script>
/**
 * showToast — Hiển thị Toast notification toàn cục
 * @param {string} message  - Nội dung hiển thị
 * @param {string} type     - 'success' | 'error' | 'warning' | 'info'
 * @param {number} duration - Thời gian tự ẩn (ms), mặc định 4000
 */
function showToast(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const configs = {
        success: { bg: 'border-green-200', iconBg: 'bg-green-100', iconColor: 'text-green-500', icon: 'fa-circle-check',        bar: 'bg-green-400' },
        error:   { bg: 'border-red-200',   iconBg: 'bg-red-100',   iconColor: 'text-red-500',   icon: 'fa-circle-exclamation',  bar: 'bg-red-400'   },
        warning: { bg: 'border-yellow-200',iconBg: 'bg-yellow-100',iconColor: 'text-yellow-500',icon: 'fa-triangle-exclamation',bar: 'bg-yellow-400'},
        info:    { bg: 'border-blue-200',  iconBg: 'bg-blue-100',  iconColor: 'text-blue-500',  icon: 'fa-circle-info',         bar: 'bg-blue-400'  }
    };

    const cfg = configs[type] || configs.info;
    const id  = 'toast-' + Date.now() + '-' + Math.random().toString(36).slice(2, 6);

    const el = document.createElement('div');
    el.id = id;
    el.className = `toast-item relative flex items-center gap-3 bg-white border shadow-lg rounded-2xl px-4 py-3.5 overflow-hidden ${cfg.bg}`;
    el.innerHTML = `
        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 ${cfg.iconBg}">
            <i class="fa-solid ${cfg.icon} ${cfg.iconColor}"></i>
        </div>
        <p class="text-sm font-medium text-gray-700 flex-1 leading-snug">${message}</p>
        <button onclick="dismissToast('${id}')" 
                class="shrink-0 w-6 h-6 flex items-center justify-center text-gray-300 hover:text-gray-500 transition rounded-full hover:bg-gray-100">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
        <div id="${id}-bar"
             class="absolute bottom-0 left-0 h-[3px] rounded-full ${cfg.bar}"
             style="width:100%; transition: width ${duration}ms linear; transition-delay: 80ms;">
        </div>
    `;

    container.appendChild(el);

    // Kích hoạt progress bar thu nhỏ dần
    requestAnimationFrame(() => requestAnimationFrame(() => {
        const bar = document.getElementById(id + '-bar');
        if (bar) bar.style.width = '0%';
    }));

    // Auto dismiss
    setTimeout(() => dismissToast(id), duration);
}

function dismissToast(id) {
    const el = document.getElementById(id);
    if (!el || el.classList.contains('hide')) return;
    el.classList.add('hide');
    setTimeout(() => el && el.remove(), 420);
}

// ── Thêm/Xóa khỏi giỏ hàng (Toggle AJAX) cho nút bên ngoài ──
function toggleCart(courseId, btnElement) {
    const isInCart = btnElement.getAttribute('data-in-cart') === 'true';
    const action = isInCart ? 'remove_from_cart' : 'add_to_cart';
    const type = btnElement.getAttribute('data-type') || 'home';
    
    fetch('?action=' + action, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ course_id: courseId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update badge count
            const badgeDesktop = document.getElementById('cart-badge-desktop');
            const badgeMobile = document.getElementById('cart-badge-mobile');
            if (badgeDesktop && badgeMobile) {
                if (data.cart_count > 0) {
                    badgeDesktop.textContent = data.cart_count;
                    badgeMobile.textContent = data.cart_count;
                    badgeDesktop.classList.remove('hidden');
                    badgeMobile.classList.remove('hidden');
                    badgeDesktop.classList.add('scale-150');
                    setTimeout(() => badgeDesktop.classList.remove('scale-150'), 200);
                } else {
                    badgeDesktop.classList.add('hidden');
                    badgeMobile.classList.add('hidden');
                }
            }
            
            // Toggle button state
            if (isInCart) {
                btnElement.setAttribute('data-in-cart', 'false');
                if (type === 'home') {
                    btnElement.className = "bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-gray-200 font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5";
                    btnElement.title = "Thêm vào giỏ hàng";
                    btnElement.innerHTML = '<i class="fa-solid fa-cart-plus"></i>';
                } else {
                    btnElement.className = "sc-btn hover:shadow-md hover:-translate-y-0.5";
                    btnElement.style = "background: white; border: 2px solid #2563eb; color: #2563eb; box-shadow: 0 2px 8px rgba(37,99,235,0.15); transition: all 0.2s;";
                    btnElement.innerHTML = '<i class="fa-solid fa-cart-plus mr-2"></i>Thêm vào giỏ hàng';
                }
                showToast("Đã xóa khỏi giỏ hàng", 'info');
            } else {
                btnElement.setAttribute('data-in-cart', 'true');
                if (type === 'home') {
                    btnElement.className = "bg-primary text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 shadow-lg transform hover:-translate-y-0.5 hover:bg-primary/90";
                    btnElement.title = "Xóa khỏi giỏ hàng";
                    btnElement.innerHTML = '<i class="fa-solid fa-cart-arrow-down"></i>';
                } else {
                    btnElement.className = "sc-btn hover:shadow-md hover:-translate-y-0.5";
                    btnElement.style = "background: #eff6ff; border: 2px solid #3b82f6; color: #1d4ed8; transition: all 0.2s;";
                    btnElement.innerHTML = '<i class="fa-solid fa-cart-arrow-down mr-2 text-blue-600"></i>Xóa khỏi giỏ hàng';
                }
                showToast("Đã thêm vào giỏ hàng", 'success');
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Không thể kết nối đến server.', 'error');
    });
}

// ── Đọc Flash Messages từ PHP Session và hiển thị Toast ──
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($_SESSION['success'])): ?>
        showToast(<?= json_encode(htmlspecialchars_decode($_SESSION['success'])) ?>, 'success');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        showToast(<?= json_encode(htmlspecialchars_decode($_SESSION['error'])) ?>, 'error');
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['warning'])): ?>
        showToast(<?= json_encode(htmlspecialchars_decode($_SESSION['warning'])) ?>, 'warning');
        <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>
});
</script>

<script>
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, easing: 'ease-in-out', once: true, mirror: false });
    }
</script>
<script src="/public/js/main.js"></script>
</body>
</html>