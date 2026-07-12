        </div> <!-- end of flex-1 content wrapper -->
    </main>

    <script>
        // Khởi tạo thư viện dùng chung cho admin
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 50 });
        }

        /**
         * showToast — Đồng bộ với global footer.php
         * @param {string} message
         * @param {string} type - 'success' | 'error' | 'warning' | 'info'
         * @param {number} duration
         */
        function showToast(message, type = 'success', duration = 4000) {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none';
                container.style = 'min-width:300px; max-width:380px;';
                document.body.appendChild(container);
            }

            const configs = {
                success: { bg: 'border-green-200', iconBg: 'bg-green-100', iconColor: 'text-green-500', icon: 'fa-circle-check',        bar: 'bg-green-400' },
                error:   { bg: 'border-red-200',   iconBg: 'bg-red-100',   iconColor: 'text-red-500',   icon: 'fa-circle-exclamation',  bar: 'bg-red-400'   },
                warning: { bg: 'border-yellow-200', iconBg: 'bg-yellow-100', iconColor: 'text-yellow-500', icon: 'fa-triangle-exclamation', bar: 'bg-yellow-400' },
                info:    { bg: 'border-blue-200',  iconBg: 'bg-blue-100',  iconColor: 'text-blue-500',  icon: 'fa-circle-info',         bar: 'bg-blue-400'  }
            };

            const cfg = configs[type] || configs.info;
            const id  = 'toast-' + Date.now() + '-' + Math.random().toString(36).slice(2, 6);

            const el = document.createElement('div');
            el.id = id;
            el.className = `pointer-events-all relative flex items-center gap-3 bg-white border shadow-lg rounded-2xl px-4 py-3.5 overflow-hidden transition-all duration-500 translate-x-full opacity-0 ${cfg.bg}`;
            el.style.pointerEvents = 'all';
            el.innerHTML = `
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 ${cfg.iconBg}">
                    <i class="fa-solid ${cfg.icon} ${cfg.iconColor}"></i>
                </div>
                <p class="text-sm font-medium text-gray-700 flex-1 leading-snug">${message}</p>
                <button onclick="dismissAdminToast('${id}')"
                        class="shrink-0 w-6 h-6 flex items-center justify-center text-gray-300 hover:text-gray-500 transition rounded-full hover:bg-gray-100">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
                <div id="${id}-bar"
                     class="absolute bottom-0 left-0 h-[3px] rounded-full ${cfg.bar}"
                     style="width:100%; transition: width ${duration}ms linear; transition-delay: 80ms;"></div>
            `;
            container.appendChild(el);

            requestAnimationFrame(() => requestAnimationFrame(() => {
                el.classList.remove('translate-x-full', 'opacity-0');
                const bar = document.getElementById(id + '-bar');
                if (bar) bar.style.width = '0%';
            }));

            setTimeout(() => dismissAdminToast(id), duration);
        }

        function dismissAdminToast(id) {
            const el = document.getElementById(id);
            if (!el || el.classList.contains('dismissing')) return;
            el.classList.add('dismissing', 'translate-x-full', 'opacity-0');
            setTimeout(() => el && el.remove(), 500);
        }

        <?php if (isset($_SESSION['success'])): ?>
            showToast(<?= json_encode(htmlspecialchars_decode($_SESSION['success'])) ?>, 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            showToast(<?= json_encode(htmlspecialchars_decode($_SESSION['error'])) ?>, 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            showToast(<?= json_encode(htmlspecialchars_decode($_SESSION['warning'])) ?>, 'warning');
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>
    </script>
    <!-- Global Scroll Lock cho Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new MutationObserver(() => {
                // Kiểm tra xem có modal overlay nào đang hiển thị không
                const openModals = document.querySelectorAll('.fixed.inset-0, .modal-overlay');
                let hasVisibleModal = false;
                for (let el of openModals) {
                    // Nếu thẻ có kích thước thực tế trên màn hình nghĩa là nó đang hiển thị
                    if (el.offsetWidth > 0 && el.offsetHeight > 0) {
                        hasVisibleModal = true;
                        break;
                    }
                }
                document.body.style.overflow = hasVisibleModal ? 'hidden' : '';
            });
            
            observer.observe(document.body, { 
                childList: true, 
                subtree: true, 
                attributes: true, 
                attributeFilter: ['style', 'class'] 
            });
        });
    </script>
</body>
</html>

